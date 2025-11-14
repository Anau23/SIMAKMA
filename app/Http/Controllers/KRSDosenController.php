<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\JadwalKuliah;
use App\Models\JadwalMatkul;
use App\Models\Kr;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KRSDosenController extends Controller
{
    public function index(Request $request)
    {
        $dosenId = Auth::user()->dosens->first()->id;
        $search = $request->get('search');

        $krs = Kr::with(['mahasiswa.user', 'matkul'])
            ->whereHas('mahasiswa', function ($q) use ($dosenId, $search) {
                $q->where('doswal_id', $dosenId);
                if ($search) {
                    $q->whereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('nim', 'like', "%{$search}%");
                    });
                }
            })
            ->orderBy('mahasiswa_id')
            ->paginate(10);

        return view('dosen.krs.index', compact('krs', 'search'));
    }

    public function show($mahasiswa_id)
    {
        $mahasiswa = Mahasiswa::with(['user', 'krs.matkul'])->findOrFail($mahasiswa_id);
        $krs = $mahasiswa->krs;

        return view('dosen.krs.show', compact('mahasiswa', 'krs'));
    }

    public function approveKrs(Request $request, $id)
    {
        $dosen = auth()->user()->dosens->first();
        $mahasiswa = Mahasiswa::where('doswal_id', $dosen->id)->findOrFail($id);

        DB::beginTransaction();
        try {
            // Get all pending KRS for this mahasiswa
            $krsList = Kr::where('mahasiswa_id', $mahasiswa->id)
                ->where('status', 'pending')
                ->with('matkul')
                ->get();

            if ($krsList->isEmpty()) {
                return redirect()->route('dosen.krs.index')
                    ->with('error', 'Tidak ada KRS yang perlu divalidasi.');
            }

            $successCount = 0;
            $failedMatkuls = [];

            foreach ($krsList as $krs) {
                // Cari jadwal yang tersedia untuk matkul ini
                $jadwalAssigned = $this->assignJadwalToMahasiswa($mahasiswa->id, $krs->matkul_id);

                if ($jadwalAssigned) {
                    // Update KRS status menjadi aktif
                    $krs->update(['status' => 'aktif']);
                    $successCount++;
                } else {
                    // Jadwal tidak bisa di-assign (bentrok atau penuh)
                    $failedMatkuls[] = $krs->matkul->name;
                    // KRS tetap pending atau bisa di-reject
                    Log::warning("Gagal assign jadwal untuk mahasiswa {$mahasiswa->nim} - Matkul: {$krs->matkul->name}");
                }
            }

            DB::commit();

            if ($successCount > 0 && empty($failedMatkuls)) {
                return redirect()->route('dosen.krs.index')
                    ->with('success', "KRS mahasiswa berhasil divalidasi. {$successCount} mata kuliah berhasil dijadwalkan.");
            } elseif ($successCount > 0 && !empty($failedMatkuls)) {
                $failedList = implode(', ', $failedMatkuls);
                return redirect()->route('dosen.krs.index')
                    ->with('warning', "KRS sebagian berhasil divalidasi. {$successCount} berhasil. Gagal: {$failedList} (bentrok jadwal atau kelas penuh).");
            } else {
                return redirect()->route('dosen.krs.index')
                    ->with('error', 'Gagal memvalidasi KRS. Semua mata kuliah bentrok jadwal atau kelas penuh.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving KRS: ' . $e->getMessage());
            return redirect()->route('dosen.krs.index')
                ->with('error', 'Terjadi kesalahan saat memvalidasi KRS: ' . $e->getMessage());
        }
    }

    /**
     * Assign jadwal mata kuliah ke mahasiswa
     * Mencari jadwal yang tidak bentrok dan masih ada kapasitas
     */
    private function assignJadwalToMahasiswa($mahasiswaId, $matkulId)
    {
        // Ambil semua jadwal yang sudah dimiliki mahasiswa
        $existingJadwals = JadwalKuliah::where('mahasiswa_id', $mahasiswaId)
            ->with('jadwal_matkul')
            ->get();

        // Ambil semua jadwal matkul yang tersedia untuk matkul ini
        $availableJadwals = JadwalMatkul::where('matkul_id', $matkulId)
            ->with(['kela', 'jadwal_kuliahs'])
            ->get();

        foreach ($availableJadwals as $jadwal) {
            // Cek kapasitas kelas
            $currentCount = $jadwal->jadwal_kuliahs->count();
            $maxKapasitas = $jadwal->kela->kapasitas ?? 40; // Default 40 jika tidak ada

            if ($currentCount >= $maxKapasitas) {
                Log::info("Kelas {$jadwal->kela->name} penuh ({$currentCount}/{$maxKapasitas})");
                continue; // Kelas penuh, coba jadwal lain
            }

            // Cek bentrok jadwal
            $isBentrok = false;
            foreach ($existingJadwals as $existing) {
                if ($this->checkJadwalBentrok($existing->jadwal_matkul, $jadwal)) {
                    $isBentrok = true;
                    Log::info("Jadwal bentrok: {$jadwal->matkul->name} ({$jadwal->hari} {$jadwal->jam_mulai->format('H:i')}) dengan {$existing->jadwal_matkul->matkul->name}");
                    break;
                }
            }

            if (!$isBentrok) {
                // Jadwal tidak bentrok dan masih ada kapasitas, assign!
                JadwalKuliah::create([
                    'jadwal_matkul_id' => $jadwal->id,
                    'mahasiswa_id' => $mahasiswaId
                ]);

                Log::info("Berhasil assign jadwal: Mahasiswa ID {$mahasiswaId} - Matkul {$jadwal->matkul->name} - Kelas {$jadwal->kela->name} - {$jadwal->hari} {$jadwal->jam_mulai->format('H:i')}-{$jadwal->jam_selesai->format('H:i')}");
                return true;
            }
        }

        return false; // Tidak ada jadwal yang cocok
    }

    /**
     * Cek apakah dua jadwal bentrok
     */
    private function checkJadwalBentrok($jadwal1, $jadwal2)
    {
        // Cek apakah hari sama
        if ($jadwal1->hari !== $jadwal2->hari) {
            return false;
        }

        // Cek apakah jam bentrok
        $start1 = $jadwal1->jam_mulai;
        $end1 = $jadwal1->jam_selesai;
        $start2 = $jadwal2->jam_mulai;
        $end2 = $jadwal2->jam_selesai;

        // Bentrok jika:
        // - start2 berada di antara start1 dan end1
        // - end2 berada di antara start1 dan end1
        // - start1 berada di antara start2 dan end2
        // - end1 berada di antara start2 dan end2
        if (
            ($start2 >= $start1 && $start2 < $end1) ||
            ($end2 > $start1 && $end2 <= $end1) ||
            ($start1 >= $start2 && $start1 < $end2) ||
            ($end1 > $start2 && $end1 <= $end2)
        ) {
            return true;
        }

        return false;
    }

    public function rejectKrs(Request $request, $id)
    {
        $dosen = auth()->user()->dosens->first();
        $mahasiswa = Mahasiswa::where('doswal_id', $dosen->id)->findOrFail($id);

        Kr::where('mahasiswa_id', $mahasiswa->id)
            ->update(['status' => 'ditolak']);

        return redirect()->route('dosen.krs.index')
            ->with('success', 'KRS mahasiswa berhasil ditolak.');
    }
}

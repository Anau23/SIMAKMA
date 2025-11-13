<?php

namespace App\Http\Controllers;

use App\Models\Kh;
use App\Models\Kr;
use App\Models\Matkul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KRSController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::user()->mahasiswas->first();

        $angkatan = (int) substr($mahasiswa->tahun_akademik, 0, 4);
        $tahunSekarang = date('Y');
        $selisihTahun = $tahunSekarang - $angkatan;
        $semester = $selisihTahun * 2 + (date('n') >= 7 ? 1 : 2);

        // Ambil KHS yang valid dan nilai IP-nya cocok
        $khs = Kh::where('mahasiswa_id', $mahasiswa->id)
            ->where('semester', $semester)
            ->where('status_verifikasi', 'valid')
            ->whereColumn('ips', '=', 'ips_ocr')
            ->first();

        $khsValid = $khs && $khs->status_verifikasi === 'valid';
        $ips = $khs->ips ?? null;
        $maxSKS = $ips ? ($ips >= 3.0 ? 24 : 20) : 0;

        // Ambil data KRS
        $krs = Kr::with(['matkul.dosen'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $availableMatkul = Matkul::with('dosen')
            ->where('prodi_id', $mahasiswa->prodi_id)
            ->whereNotIn('id', $krs->pluck('matkul_id'))
            ->orderBy('semester')
            ->orderBy('name')
            ->get();

        $totalSKS = $krs->sum(fn($item) => $item->matkul->sks);

        return view('mahasiswa.krs.index', compact(
            'krs',
            'availableMatkul',
            'totalSKS',
            'semester',
            'khsValid',
            'ips',
            'maxSKS'
        ));
    }


    public function store(Request $request)
    {
        $mahasiswa = Auth::user()->mahasiswas->first();

        $request->validate([
            'matkul_id' => 'required|exists:matkuls,id',
        ]);

        $angkatan = (int) substr($mahasiswa->tahun_akademik, 0, 4);
        $tahunSekarang = date('Y');
        $selisihTahun = $tahunSekarang - $angkatan;
        $semester = $selisihTahun * 2 + (date('n') >= 7 ? 1 : 2);

        $khs = Kh::where('mahasiswa_id', $mahasiswa->id)
            ->where('semester', $semester)
            ->where('status_verifikasi', 'valid')
            ->whereColumn('ips', '=', 'ips_ocr')
            ->first();

        // Tentukan batas maksimal SKS
        $maxSKS = $khs->ips >= 3.0 ? 24 : 20;

        // Ambil total SKS yang sudah diambil
        $totalSKS = Kr::where('mahasiswa_id', $mahasiswa->id)
            ->with('matkul')
            ->get()
            ->sum(fn($item) => $item->matkul->sks);

        $matkul = Matkul::findOrFail($request->matkul_id);

        // Jika SKS baru akan melebihi batas
        if (($totalSKS + $matkul->sks) > $maxSKS) {
            return redirect()->back()->with('error', "Total SKS melebihi batas. Maksimal {$maxSKS} SKS untuk IP {$khs->ips}.");
        }

        // Check if matkul already in KRS
        $existingKRS = Kr::where('mahasiswa_id', $mahasiswa->id)
            ->where('matkul_id', $request->matkul_id)
            ->first();

        if ($existingKRS) {
            return redirect()->back()->with('error', 'Mata kuliah sudah ada dalam KRS.');
        }

        // Check if matkul belongs to same prodi
        $matkul = Matkul::find($request->matkul_id);
        if ($matkul->prodi_id != $mahasiswa->prodi_id) {
            return redirect()->back()->with('error', 'Mata kuliah tidak tersedia untuk program studi Anda.');
        }

        // Create KRS with pending status
        Kr::create([
            'mahasiswa_id' => $mahasiswa->id,
            'matkul_id' => $request->matkul_id,
            'status' => 'pending'
        ]);

        return redirect()->route('mahasiswa.krs.index')
            ->with('success', 'Mata kuliah berhasil ditambahkan ke KRS. Menunggu persetujuan dosen wali.');
    }

    public function destroy(Kr $krs)
    {
        if ($krs->mahasiswa_id != Auth::user()->mahasiswas->first()->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus KRS ini.');
        }

        if ($krs->status != 'pending') {
            return redirect()->back()->with('error', 'Hanya KRS dengan status pending yang dapat dihapus.');
        }

        $krs->delete();

        return redirect()->route('mahasiswa.krs.index')
            ->with('success', 'Mata kuliah berhasil dihapus dari KRS.');
    }
}

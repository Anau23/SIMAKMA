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
        $khs = Kh::where('mahasiswa_id', $mahasiswa->id)
            ->where('semester', $semester)
            ->where('status_verifikasi', 'valid')
            ->whereColumn('ips', '=', 'ips_ocr')
            ->first();

        $khsValid = $khs && $khs->status_verifikasi === 'valid';
        $ips = $khs->ips ?? null;
        $maxSKS = $ips ? ($ips >= 3.0 ? 24 : 20) : 0;
        $krs = Kr::with(['matkul.dosen'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('created_at', 'desc')
            ->get();
        $tempKRSIds = session('temp_krs', []);
        $tempKRS = Matkul::with('dosen')
            ->whereIn('id', $tempKRSIds)
            ->get();
        $usedMatkulIds = array_merge(
            $krs->pluck('matkul_id')->toArray(),
            $tempKRSIds
        );

        $availableMatkul = Matkul::with('dosen')
            ->where('prodi_id', $mahasiswa->prodi_id)
            ->whereNotIn('id', $usedMatkulIds)
            ->orderBy('semester')
            ->orderBy('name')
            ->get();
        $totalSKS = $krs->sum(fn($item) => $item->matkul->sks) + $tempKRS->sum('sks');
        $hasFinalized = $krs->count() > 0;

        return view('mahasiswa.krs.index', compact(
            'krs',
            'tempKRS',
            'availableMatkul',
            'totalSKS',
            'semester',
            'khsValid',
            'ips',
            'maxSKS',
            'hasFinalized'
        ));
    }

    public function addTemp(Request $request)
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

        if (!$khs) {
            return redirect()->back()->with('error', 'KHS belum valid. Silakan upload KHS terlebih dahulu.');
        }

        $maxSKS = $khs->ips >= 3.0 ? 24 : 20;
        $tempKRSIds = session('temp_krs', []);
        if (in_array($request->matkul_id, $tempKRSIds)) {
            return redirect()->back()->with('error', 'Mata kuliah sudah ada dalam daftar.');
        }
        $existingKRS = Kr::where('mahasiswa_id', $mahasiswa->id)
            ->where('matkul_id', $request->matkul_id)
            ->first();

        if ($existingKRS) {
            return redirect()->back()->with('error', 'Mata kuliah sudah ada dalam KRS yang tersimpan.');
        }
        $tempMatkuls = Matkul::whereIn('id', $tempKRSIds)->get();
        $savedKRS = Kr::where('mahasiswa_id', $mahasiswa->id)->with('matkul')->get();

        $totalTempSKS = $tempMatkuls->sum('sks');
        $totalSavedSKS = $savedKRS->sum(fn($item) => $item->matkul->sks);

        $matkul = Matkul::findOrFail($request->matkul_id);

        if (($totalTempSKS + $totalSavedSKS + $matkul->sks) > $maxSKS) {
            return redirect()->back()->with('error', "Total SKS melebihi batas. Maksimal {$maxSKS} SKS untuk IP {$khs->ips}.");
        }
        if ($matkul->prodi_id != $mahasiswa->prodi_id) {
            return redirect()->back()->with('error', 'Mata kuliah tidak tersedia untuk program studi Anda.');
        }
        $tempKRSIds[] = $request->matkul_id;
        session(['temp_krs' => $tempKRSIds]);

        return redirect()->route('mahasiswa.krs.index')
            ->with('success', 'Mata kuliah berhasil ditambahkan ke daftar. Klik Finalisasi KRS untuk menyimpan.');
    }

    public function removeTemp($matkulId)
    {
        $tempKRSIds = session('temp_krs', []);

        $tempKRSIds = array_diff($tempKRSIds, [$matkulId]);

        session(['temp_krs' => array_values($tempKRSIds)]);

        return redirect()->route('mahasiswa.krs.index')
            ->with('success', 'Mata kuliah berhasil dihapus dari daftar.');
    }

    public function finalize()
    {
        $mahasiswa = Auth::user()->mahasiswas->first();
        $tempKRSIds = session('temp_krs', []);

        if (empty($tempKRSIds)) {
            return redirect()->back()->with('error', 'Tidak ada mata kuliah untuk difinalisasi.');
        }

        foreach ($tempKRSIds as $matkulId) {
            Kr::create([
                'mahasiswa_id' => $mahasiswa->id,
                'matkul_id' => $matkulId,
                'status' => 'pending'
            ]);
        }

        session()->forget('temp_krs');

        return redirect()->route('mahasiswa.krs.index')
            ->with('success', 'KRS berhasil difinalisasi dan dikirim ke dosen wali untuk persetujuan.');
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

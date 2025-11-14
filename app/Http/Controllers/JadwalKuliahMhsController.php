<?php

namespace App\Http\Controllers;

use App\Models\JadwalKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalKuliahMhsController extends Controller
{
    public function index(Request $request)
    {
        $mahasiswa = Auth::user()->mahasiswas->first();
        $angkatan = (int) substr($mahasiswa->tahun_akademik, 0, 4);
        $tahunSekarang = date('Y');
        $selisihTahun = $tahunSekarang - $angkatan;
        $semester = $selisihTahun * 2 + (date('n') >= 7 ? 1 : 2);

        $search = $request->query('search');

        $jadwals = JadwalKuliah::where('mahasiswa_id', $mahasiswa->id)
            ->whereHas('jadwal_matkul', function ($q) use ($search) {
                if ($search) {
                    $q->whereHas('matkul', fn($q2) => $q2->where('name', 'like', "%$search%"))
                        ->orWhereHas('kela', fn($q2) => $q2->where('name', 'like', "%$search%"))
                        ->orWhere('hari', 'like', "%$search%");
                }
            })
            ->with(['jadwal_matkul.matkul.dosen', 'jadwal_matkul.kela'])
            ->get()
            ->sortBy(function ($jadwal) {
                $hariUrutan = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6];
                return [$hariUrutan[$jadwal->jadwal_matkul->hari] ?? 7, $jadwal->jadwal_matkul->jam_mulai];
            });
        $perPage = 5;
        $page = $request->query('page', 1);
        $total = $jadwals->count();
        $jadwalsPage = $jadwals->slice(($page - 1) * $perPage, $perPage)->values();
        $totalSKS = $jadwals->sum(fn($j) => $j->jadwal_matkul->matkul->sks);
        $tahunAkademik = $mahasiswa->tahun_akademik;
        return view('mahasiswa.jadwal_kuliah.index', [
            'jadwals' => $jadwalsPage,
            'totalJadwal' => $total,
            'semester' => $semester,
            'tahunAkademik' => $tahunAkademik,
            'totalSKS' => $totalSKS,
            'perPage' => $perPage,
            'currentPage' => $page,
            'search' => $search
        ]);
    }
}

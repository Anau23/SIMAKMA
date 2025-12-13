<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalKuliahMhsController extends Controller
{
    public function index(Request $request)
    {
        $mahasiswa = Auth::user()->mahasiswas;

        if (!$mahasiswa) {
            return response()->json([
                'status' => false,
                'message' => 'Mahasiswa tidak ditemukan.'
            ], 404);
        }

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
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'status' => true,
            'semester' => $semester,
            'tahun_akademik' => $mahasiswa->tahun_akademik,
            'total_sks' => $jadwals->sum(fn($j) => $j->jadwal_matkul->matkul->sks),
            'data' => $jadwals
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Kr;
use App\Models\Matkul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KRSController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::user()->mahasiswa;

        // Get KRS for current mahasiswa
        $krs = Kr::with(['matkuls.dosen'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get available matkul that haven't been added to KRS and belong to same prodi
        $availableMatkul = Matkul::with('dosen')
            ->where('prodi_id', $mahasiswa->prodi_id)
            ->whereNotIn('id', $krs->pluck('matkul_id'))
            ->orderBy('semester')
            ->orderBy('name')
            ->get();

        // Calculate total SKS from approved and pending KRS
        $totalSKS = $krs->sum(function ($item) {
            return $item->matkul->sks;
        });

        return view('mahasiswa.krs.index', compact('krs', 'availableMatkul', 'totalSKS'));
    }

    public function store(Request $request)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        $request->validate([
            'matkul_id' => 'required|exists:matkuls,id',
        ]);

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

    public function destroy(Kr $kr)
    {
        // Check if KRS belongs to current mahasiswa
        if ($kr->mahasiswa_id != Auth::user()->mahasiswa->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus KRS ini.');
        }

        // Only allow deletion if status is pending
        if ($kr->status != 'pending') {
            return redirect()->back()->with('error', 'Hanya KRS dengan status pending yang dapat dihapus.');
        }

        $kr->delete();

        return redirect()->route('mahasiswa.krs.index')
            ->with('success', 'Mata kuliah berhasil dihapus dari KRS.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Kr;
use Illuminate\Http\Request;

class KRSController extends Controller
{
    public function index()
    {
        $krs = Kr::with(['mahasiswa.user', 'matkul'])->get();
        return view('admin.krs.index', compact('krs'));
    }

    public function approve(Kr $krs)
    {
        $krs->update(['status' => 'aktif']);
        return redirect()->back()->with('success', 'KRS berhasil disetujui.');
    }

    public function reject(Kr $krs)
    {
        $krs->update(['status' => 'ditolak']);
        return redirect()->back()->with('success', 'KRS berhasil ditolak.');
    }
}

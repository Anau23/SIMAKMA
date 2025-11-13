<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kr;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        Kr::where('mahasiswa_id', $mahasiswa->id)
            ->update(['status' => 'aktif']);

        return redirect()->route('dosen.krs.index')
            ->with('success', 'KRS mahasiswa berhasil divalidasi.');
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

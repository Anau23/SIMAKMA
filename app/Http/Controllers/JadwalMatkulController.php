<?php

namespace App\Http\Controllers;

use App\Models\JadwalMatkul;
use App\Models\Kela;
use App\Models\Matkul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JadwalMatkulController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $jadwals = JadwalMatkul::with(['kela', 'matkul'])
            ->when($search, function($q, $search) {
                return $q->whereHas('kela', function($q2) use ($search){
                    $q2->where('name', 'like', "%$search%");
                })->orWhereHas('matkul', function($q2) use ($search){
                    $q2->where('name', 'like', "%$search%");
                })->orWhere('hari', 'like', "%$search%");
            })
            ->orderBy('hari')
            ->paginate(10)
            ->withQueryString();

        return view('admin.jadwal_matkul.index', compact('jadwals', 'search'));
    }

    public function create()
    {
        $kelas = Kela::orderBy('name')->get();
        $matkuls = Matkul::orderBy('name')->get();
        return view('admin.jadwal_matkul.create', compact('kelas', 'matkuls'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kelas_id' => 'required|exists:kelas,id',
            'matkul_id' => 'required|exists:matkuls,id',
            'hari' => 'required|string|max:50',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        JadwalMatkul::create($request->all());

        return redirect()->route('admin.jadwal_matkul.index')
            ->with('success', 'Jadwal mata kuliah berhasil ditambahkan.');
    }

    public function edit(JadwalMatkul $jadwal_matkul)
    {
        $kelas = Kela::orderBy('name')->get();
        $matkuls = Matkul::orderBy('name')->get();
        return view('admin.jadwal_matkul.edit', compact('jadwal_matkul', 'kelas', 'matkuls'));
    }

    public function update(Request $request, JadwalMatkul $jadwal_matkul)
    {
        $validator = Validator::make($request->all(), [
            'kelas_id' => 'required|exists:kelas,id',
            'matkul_id' => 'required|exists:matkuls,id',
            'hari' => 'required|string|max:50',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $jadwal_matkul->update($request->all());

        return redirect()->route('admin.jadwal_matkul.index')
            ->with('success', 'Jadwal mata kuliah berhasil diupdate.');
    }

    public function destroy(JadwalMatkul $jadwal_matkul)
    {
        $jadwal_matkul->delete();
        return redirect()->route('admin.jadwal_matkul.index')
            ->with('success', 'Jadwal mata kuliah berhasil dihapus.');
    }
}

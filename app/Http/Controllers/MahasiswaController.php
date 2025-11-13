<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matkul;
use App\Models\Prodi;
use App\Models\Dosen;

class MatkulController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Matkul::with(['prodi', 'dosen']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_mk', 'like', '%' . $search . '%')
                  ->orWhere('name', 'like', '%' . $search . '%')
                  ->orWhereHas('prodi', fn($sub) => $sub->where('name', 'like', '%' . $search . '%'))
                  ->orWhereHas('dosen', fn($sub) => $sub->where('nama_dosen', 'like', '%' . $search . '%'));
            });
        }

        $matkul = $query->orderBy('kode_mk')->paginate(10);

        return view('admin.matkul.index', compact('matkul', 'search'));
    }

    public function create()
    {
        $prodi = Prodi::all();
        $dosen = Dosen::all();
        return view('admin.matkul.create', compact('prodi', 'dosen'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_mk' => 'required|unique:matkul',
            'name' => 'required',
            'sks' => 'required|integer',
            'semester' => 'required|integer',
            'prodi_id' => 'required|exists:prodi,id',
            'dosen_id' => 'required|exists:dosen,id',
        ]);

        Matkul::create($request->all());

        return redirect()->route('admin.matkul.index')->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function edit(Matkul $matkul)
    {
        $prodi = Prodi::all();
        $dosen = Dosen::all();
        return view('admin.matkul.edit', compact('matkul', 'prodi', 'dosen'));
    }

    public function update(Request $request, Matkul $matkul)
    {
        $request->validate([
            'kode_mk' => 'required|unique:matkul,kode_mk,' . $matkul->id,
            'name' => 'required',
            'sks' => 'required|integer',
            'semester' => 'required|integer',
            'prodi_id' => 'required|exists:prodi,id',
            'dosen_id' => 'required|exists:dosen,id',
        ]);

        $matkul->update($request->all());

        return redirect()->route('admin.matkul.index')->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    public function destroy(Matkul $matkul)
    {
        $matkul->delete();
        return redirect()->route('admin.matkul.index')->with('success', 'Mata kuliah berhasil dihapus.');
    }
}

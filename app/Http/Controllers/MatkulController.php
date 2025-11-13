<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Matkul;
use App\Models\Prodi;
use App\Models\Dosen;
class MatkulController extends Controller
{
       public function index()
    {
        $matkul = Matkul::with(['prodi', 'dosen'])->get();
        return view('admin.matkul.index', compact('matkul'));
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

    public function show(Matkul $matkul)
    {
        return view('admin.matkul.show', compact('matkul'));
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

        return redirect()->route('admin.matkul.index')->with('success', 'Mata kuliah berhasil diupdate.');
    }

    public function destroy(Matkul $matkul)
    {
        $matkul->delete();
        return redirect()->route('admin.matkul.index')->with('success', 'Mata kuliah berhasil dihapus.');
    }
}

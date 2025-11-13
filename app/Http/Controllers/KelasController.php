<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kela;

class KelasController extends Controller
{
    
    public function index()
    {
        $kelas = Kela::all();
        return view('admin.kelas.index', compact('kelas'));
    }

    public function create()
    {
        return view('admin.kelas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ruang' => 'required|string|max:255',
        ]);

        Kela::create($request->all());

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(Kela $kela)
    {
        $kela->load('jadwal_matkuls.matkul');
        return view('admin.kelas.show', compact('kela'));
    }

    public function edit(Kela $kela)
    {
        return view('admin.kelas.edit', compact('kela'));
    }

    public function update(Request $request, Kela $kela)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ruang' => 'required|string|max:255',
        ]);

        $kela->update($request->all());

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil diupdate.');
    }

    public function destroy(Kela $kela)
    {
        // Check if kelas has jadwal matkul
        if ($kela->jadwal_matkuls()->count() > 0) {
            return redirect()->route('admin.kelas.index')
                ->with('error', 'Tidak dapat menghapus kelas karena masih memiliki jadwal mata kuliah.');
        }

        $kela->delete();

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}

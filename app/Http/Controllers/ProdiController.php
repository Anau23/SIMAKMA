<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use App\Models\Fakulta;
use Illuminate\Http\Request;

class ProdiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $prodi = Prodi::with('fakulta')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.prodi.index', compact('prodi', 'search'));
    }

    public function create()
    {
        $fakultas = Fakulta::all();
        return view('admin.prodi.create', compact('fakultas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'fakultas_id' => 'required|exists:fakultas,id',
        ]);

        Prodi::create($request->only('fakultas_id', 'name'));

        return redirect()->route('admin.prodi.index')->with('success', 'Data prodi berhasil ditambahkan!');
    }

    public function edit(Prodi $prodi)
    {
        $fakultas = Fakulta::all();
        return view('admin.prodi.edit', compact('prodi', 'fakultas'));
    }

    public function update(Request $request, Prodi $prodi)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'fakultas_id' => 'required|exists:fakultas,id',
        ]);

        $prodi->update($request->only('fakultas_id', 'name'));

        return redirect()->route('admin.prodi.index')->with('success', 'Data prodi berhasil diperbarui!');
    }

    public function destroy(Prodi $prodi)
    {
        $prodi->delete();
        return redirect()->route('admin.prodi.index')->with('success', 'Data prodi berhasil dihapus!');
    }
}

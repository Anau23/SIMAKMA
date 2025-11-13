<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fakulta;

class FakultasController extends Controller
{

    public function index()
    {
        $fakultas = Fakulta::all();
        return view('admin.fakultas.index', compact('fakultas'));
    }


    public function create()
    {
        return view('admin.fakultas.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:fakultas,name',
        ]);

        Fakulta::create($request->all());

        return redirect()->route('admin.fakultas.index')
            ->with('success', 'Fakultas berhasil ditambahkan.');
    }


    public function show(Fakulta $fakulta)
    {
        return view('admin.fakultas.show', compact('fakulta'));
    }

    public function edit(Fakulta $fakulta)
    {
        return view('admin.fakultas.edit', compact('fakulta'));
    }

    public function update(Request $request, Fakulta $fakulta)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:fakultas,name,' . $fakulta->id,
        ]);

        $fakulta->update($request->all());

        return redirect()->route('admin.fakultas.index')
            ->with('success', 'Fakultas berhasil diupdate.');
    }

    public function destroy(Fakulta $fakulta)
    {
        if ($fakulta->prodis()->count() > 0) {
            return redirect()->route('admin.fakultas.index')
                ->with('error', 'Tidak dapat menghapus fakultas karena masih memiliki program studi.');
        }

        $fakulta->delete();

        return redirect()->route('admin.fakultas.index')
            ->with('success', 'Fakultas berhasil dihapus.');
    }
}

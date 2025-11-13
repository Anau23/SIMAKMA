<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fakulta;
use Illuminate\Support\Facades\Validator;

class FakultasController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $fakultas = Fakulta::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%");
        })
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('admin.fakultas.index', compact('fakultas', 'search'));
    }

    public function create()
    {
        return view('admin.fakultas.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:fakultas,name',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $errorMessage = "Tambah data gagal. periksa kembali data yang diinput.<br><ul>";
            foreach ($errors as $error) {
                $errorMessage .= "<li>$error</li>";
            }
            $errorMessage .= "</ul>";
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }

        Fakulta::create($request->only('name'));

        return redirect()->route('admin.fakultas.index')->with('success', 'Fakultas berhasil ditambahkan.');
    }

    public function edit(Fakulta $fakulta)
    {
        return view('admin.fakultas.edit', compact('fakulta'));
    }

    public function update(Request $request, Fakulta $fakulta)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:fakultas,name,' . $fakulta->id,
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $errorMessage = "Update data gagal. periksa kembali data yang diinput.<br><ul>";
            foreach ($errors as $error) {
                $errorMessage .= "<li>$error</li>";
            }
            $errorMessage .= "</ul>";
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }

        $fakulta->update($request->only('name'));

        return redirect()->route('admin.fakultas.index')->with('success', 'Fakultas berhasil diupdate.');
    }

    public function destroy(Fakulta $fakulta)
    {
        if ($fakulta->prodis()->count() > 0) {
            return redirect()->route('admin.fakultas.index')
                ->with('error', 'Tidak dapat menghapus fakultas karena masih memiliki program studi.');
        }

        $fakulta->delete();

        return redirect()->route('admin.fakultas.index')->with('success', 'Fakultas berhasil dihapus.');
    }
}

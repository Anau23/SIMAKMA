<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kela;
use Illuminate\Support\Facades\Validator;

class KelasController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->query('search');

        $kelas = Kela::when($search, function ($q, $search) {
            return $q->where('name', 'like', "%$search%")
                ->orWhere('ruang', 'like', "%$search%")
                ->orWhere('kapasitas', 'like', "%$search%");
        })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.kelas.index', compact('kelas', 'search'));
    }

    public function create()
    {
        return view('admin.kelas.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'ruang' => 'required|string|max:255',
            'kapasitas' => 'required|integer',
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'ruang' => 'required|string|max:255',
            'kapasitas' => 'required|integer',
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

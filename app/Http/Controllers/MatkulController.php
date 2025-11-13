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

        // Query dasar dengan relasi
        $query = Matkul::with(['prodi', 'dosen']);

        // Jika ada pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('kode_mk', 'like', '%' . $search . '%')
                    ->orWhereHas('prodi', function ($q2) use ($search) {
                        $q2->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('dosen', function ($q3) use ($search) {
                        $q3->where('nama_dosen', 'like', '%' . $search . '%');
                    });
            });
        }

        // Pagination (10 item per halaman)
        $matkul = $query->orderBy('kode_mk')->paginate(10);

        // Kirim data ke view
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
            'kode_mk' => 'required|unique:matkuls',
            'name' => 'required',
            'sks' => 'required|integer',
            'semester' => 'required|integer',
            'prodi_id' => 'required|exists:prodis,id',
            'dosen_id' => 'required|exists:dosens,id',
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
            'kode_mk' => 'required|unique:matkuls,kode_mk,' . $matkul->id,
            'name' => 'required',
            'sks' => 'required|integer',
            'semester' => 'required|integer',
            'prodi_id' => 'required|exists:prodis,id',
            'dosen_id' => 'required|exists:dosens,id',
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

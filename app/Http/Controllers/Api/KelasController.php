<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kela;
use Illuminate\Support\Facades\Validator;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $kelas = Kela::when($search, function ($q) use ($search) {
            return $q->where('name', 'like', "%$search%")
                ->orWhere('ruang', 'like', "%$search%")
                ->orWhere('kapasitas', 'like', "%$search%");
        })
            ->orderBy('name')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Daftar kelas berhasil diambil.',
            'data' => $kelas
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'ruang' => 'required|string|max:255',
            'kapasitas' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Tambah data gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $kelas = Kela::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil ditambahkan.',
            'data' => $kelas
        ]);
    }

    public function show(Kela $kela)
    {
        $kela->load('jadwal_matkuls.matkul');

        return response()->json([
            'success' => true,
            'message' => 'Detail kelas berhasil diambil.',
            'data' => $kela
        ]);
    }

    public function update(Request $request, Kela $kela)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'ruang' => 'required|string|max:255',
            'kapasitas' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Update data gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $kela->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil diupdate.',
            'data' => $kela
        ]);
    }

    public function destroy(Kela $kela)
    {
        if ($kela->jadwal_matkuls()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus kelas karena masih memiliki jadwal mata kuliah.'
            ], 400);
        }

        $kela->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil dihapus.'
        ]);
    }
}

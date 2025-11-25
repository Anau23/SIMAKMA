<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Matkul;
use App\Models\Prodi;
use App\Models\Dosen;
use Illuminate\Support\Facades\Validator;

class MatkulController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Matkul::with(['prodi', 'dosen']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('kode_mk', 'like', "%{$search}%")
                    ->orWhereHas('prodi', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('dosen', function ($q3) use ($search) {
                        $q3->where('nama_dosen', 'like', "%{$search}%");
                    });
            });
        }

        $matkul = $query->orderBy('kode_mk')->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Daftar mata kuliah berhasil diambil.',
            'data' => $matkul
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_mk'   => 'required|unique:matkuls',
            'name'      => 'required',
            'sks'       => 'required|integer',
            'semester'  => 'required|integer',
            'prodi_id'  => 'required|exists:prodis,id',
            'dosen_id'  => 'required|exists:dosens,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Tambah data gagal.',
                'errors'  => $validator->errors()
            ], 422);
        }

        $matkul = Matkul::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Mata kuliah berhasil ditambahkan.',
            'data' => $matkul
        ]);
    }

    public function show(Matkul $matkul)
    {
        $matkul->load(['prodi', 'dosen']);

        return response()->json([
            'success' => true,
            'message' => 'Detail mata kuliah berhasil diambil.',
            'data' => $matkul
        ]);
    }

    public function update(Request $request, Matkul $matkul)
    {
        $validator = Validator::make($request->all(), [
            'kode_mk'   => 'required|unique:matkuls,kode_mk,' . $matkul->id,
            'name'      => 'required',
            'sks'       => 'required|integer',
            'semester'  => 'required|integer',
            'prodi_id'  => 'required|exists:prodis,id',
            'dosen_id'  => 'required|exists:dosens,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Update data gagal.',
                'errors'  => $validator->errors()
            ], 422);
        }

        $matkul->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Mata kuliah berhasil diupdate.',
            'data' => $matkul
        ]);
    }

    public function destroy(Matkul $matkul)
    {
        $matkul->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mata kuliah berhasil dihapus.'
        ]);
    }
}

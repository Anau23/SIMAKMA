<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fakulta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FakultasController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $fakultas = Fakulta::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%");
        })
            ->orderBy('name', 'asc')
            ->paginate(10);

        return response()->json([
            'status'  => true,
            'message' => 'Data fakultas ditemukan',
            'data'    => $fakultas
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:fakultas,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $fakultas = Fakulta::create($request->only('name'));

        return response()->json([
            'status'  => true,
            'message' => 'Fakultas berhasil ditambahkan',
            'data'    => $fakultas
        ]);
    }

    public function show($id)
    {
        $fakultas = Fakulta::find($id);

        if (!$fakultas) {
            return response()->json([
                'status'  => false,
                'message' => 'Fakultas tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Detail fakultas ditemukan',
            'data'    => $fakultas
        ]);
    }

    public function update(Request $request, $id)
    {
        $fakultas = Fakulta::find($id);

        if (!$fakultas) {
            return response()->json([
                'status'  => false,
                'message' => 'Fakultas tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:fakultas,name,' . $fakultas->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $fakultas->update($request->only('name'));

        return response()->json([
            'status'  => true,
            'message' => 'Fakultas berhasil diperbarui',
            'data'    => $fakultas
        ]);
    }

    public function destroy($id)
    {
        $fakultas = Fakulta::find($id);

        if (!$fakultas) {
            return response()->json([
                'status'  => false,
                'message' => 'Fakultas tidak ditemukan'
            ], 404);
        }

        if ($fakultas->prodis()->count() > 0) {
            return response()->json([
                'status'  => false,
                'message' => 'Tidak dapat menghapus fakultas karena masih memiliki program studi'
            ], 409); 
        }

        $fakultas->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Fakultas berhasil dihapus'
        ]);
    }
}

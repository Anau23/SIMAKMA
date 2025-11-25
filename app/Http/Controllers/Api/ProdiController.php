<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use App\Models\Fakulta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProdiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $prodi = Prodi::with('fakulta')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return response()->json([
            'status'  => true,
            'message' => 'Data prodi ditemukan',
            'data'    => $prodi
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'fakultas_id'  => 'required|exists:fakultas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $prodi = Prodi::create($request->only('fakultas_id', 'name'));

        return response()->json([
            'status'  => true,
            'message' => 'Data prodi berhasil ditambahkan',
            'data'    => $prodi->load('fakulta')
        ]);
    }

    public function show($id)
    {
        $prodi = Prodi::with('fakulta')->find($id);

        if (!$prodi) {
            return response()->json([
                'status'  => false,
                'message' => 'Data prodi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Detail prodi ditemukan',
            'data'    => $prodi
        ]);
    }

    public function update(Request $request, $id)
    {
        $prodi = Prodi::find($id);

        if (!$prodi) {
            return response()->json([
                'status'  => false,
                'message' => 'Data prodi tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'fakultas_id'  => 'required|exists:fakultas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $prodi->update($request->only('fakultas_id', 'name'));

        return response()->json([
            'status'  => true,
            'message' => 'Data prodi berhasil diupdate',
            'data'    => $prodi->load('fakulta')
        ]);
    }

    public function destroy($id)
    {
        $prodi = Prodi::find($id);

        if (!$prodi) {
            return response()->json([
                'status'  => false,
                'message' => 'Data prodi tidak ditemukan'
            ], 404);
        }

        $prodi->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Data prodi berhasil dihapus'
        ]);
    }
}

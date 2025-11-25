<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DosenController extends Controller
{
    // GET /api/dosens
    public function index()
    {
        $dosen = Dosen::with(['user', 'prodi'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $dosen
        ]);
    }

    // POST /api/dosens
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'nip' => 'required|unique:dosens',
            'prodi_id' => 'required|exists:prodis,id',
            'alamat' => 'required',
            'gender' => 'required|in:L,P',
            'religion' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'dosen',
            'status' => 'aktif'
        ]);

        $dosen = Dosen::create([
            'user_id' => $user->id,
            'prodi_id' => $request->prodi_id,
            'name' => $request->name,
            'nip' => $request->nip,
            'alamat' => $request->alamat,
            'gender' => $request->gender,
            'religion' => $request->religion,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dosen berhasil ditambahkan!',
            'data' => $dosen
        ], 201);
    }

    // GET /api/dosens/{id}
    public function show($id)
    {
        $dosen = Dosen::with(['user', 'prodi'])->find($id);

        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Dosen tidak ditemukan!'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $dosen
        ]);
    }

    // PUT /api/dosens/{id}
    public function update(Request $request, $id)
    {
        $dosen = Dosen::find($id);

        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Dosen tidak ditemukan!'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $dosen->user_id,
            'nip' => 'required|unique:dosens,nip,' . $dosen->id,
            'prodi_id' => 'required|exists:prodis,id',
            'alamat' => 'required',
            'gender' => 'required|in:L,P',
            'religion' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $dosen->user->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        $dosen->update($request->only([
            'prodi_id', 'nip', 'alamat', 'gender', 'religion'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Dosen berhasil diperbarui!',
            'data' => $dosen
        ]);
    }

    // DELETE /api/dosens/{id}
    public function destroy($id)
    {
        $dosen = Dosen::find($id);

        if (!$dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Dosen tidak ditemukan!'
            ], 404);
        }

        $dosen->user->delete();
        $dosen->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dosen berhasil dihapus!'
        ]);
    }
}

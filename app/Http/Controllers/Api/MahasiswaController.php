<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\Dosen;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $query = Mahasiswa::with(['user', 'dosen', 'prodi']);

        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            })
                ->orWhere('nim', 'like', "%$search%")
                ->orWhereHas('prodi', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
        }

        return response()->json([
            'status' => true,
            'message' => 'Data mahasiswa ditemukan',
            'data' => $query->orderBy('nim')->paginate(10)
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required',
            'email'          => 'required|email|unique:users',
            'password'       => 'required|min:8',
            'nim'            => 'required|unique:mahasiswas',
            'doswal_id'      => 'required|exists:dosens,id',
            'prodi_id'       => 'required|exists:prodis,id',
            'angkatan'       => 'required',
            'alamat'         => 'required',
            'no_telp'        => 'required',
            'gender'         => 'required|in:L,P',
            'religion'       => 'required',
            'tahun_akademik' => 'required|digits:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'mahasiswa',
            'status'   => 'aktif',
        ]);

        $mahasiswa = Mahasiswa::create([
            'user_id'        => $user->id,
            'doswal_id'      => $request->doswal_id,
            'prodi_id'       => $request->prodi_id,
            'nim'            => $request->nim,
            'angkatan'       => $request->angkatan,
            'alamat'         => $request->alamat,
            'no_telp'        => $request->no_telp,
            'gender'         => $request->gender,
            'religion'       => $request->religion,
            'tahun_akademik' => $request->tahun_akademik,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Mahasiswa berhasil ditambahkan',
            'data'    => $mahasiswa->load(['user', 'dosen', 'prodi'])
        ]);
    }

    public function show($id)
    {
        $mahasiswa = Mahasiswa::with(['user', 'dosen', 'prodi'])->find($id);

        if (!$mahasiswa) {
            return response()->json([
                'status' => false,
                'message' => 'Mahasiswa tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Detail mahasiswa',
            'data'    => $mahasiswa
        ]);
    }

    public function update(Request $request, $id)
    {
        $mahasiswa = Mahasiswa::find($id);

        if (!$mahasiswa) {
            return response()->json([
                'status' => false,
                'message' => 'Mahasiswa tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'           => 'required',
            'email'          => 'required|email|unique:users,email,' . $mahasiswa->user_id,
            'nim'            => 'required|unique:mahasiswas,nim,' . $mahasiswa->id,
            'doswal_id'      => 'required|exists:dosens,id',
            'prodi_id'       => 'required|exists:prodis,id',
            'angkatan'       => 'required',
            'alamat'         => 'required',
            'no_telp'        => 'required',
            'gender'         => 'required|in:L,P',
            'religion'       => 'required',
            'tahun_akademik' => 'required|digits:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $mahasiswa->user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        $mahasiswa->update($request->only([
            'doswal_id',
            'prodi_id',
            'nim',
            'angkatan',
            'alamat',
            'no_telp',
            'gender',
            'religion',
            'tahun_akademik'
        ]));

        return response()->json([
            'status'  => true,
            'message' => 'Mahasiswa berhasil diupdate',
            'data'    => $mahasiswa->load(['user', 'dosen', 'prodi'])
        ]);
    }

    public function destroy($id)
    {
        $mahasiswa = Mahasiswa::find($id);

        if (!$mahasiswa) {
            return response()->json([
                'status' => false,
                'message' => 'Mahasiswa tidak ditemukan'
            ], 404);
        }

        $mahasiswa->user->delete();
        $mahasiswa->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Mahasiswa berhasil dihapus'
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\Dosen;
use App\Models\Prodi;

class MahasiswaController extends Controller
{
    public function index()
    {
        $mahasiswa = Mahasiswa::with(['user', 'dosenWali', 'prodi'])->get();
        return view('admin.mahasiswa.index', compact('mahasiswa'));
    }

    public function create()
    {
        $dosen = Dosen::all();
        $prodi = Prodi::all();
        return view('admin.mahasiswa.create', compact('dosen', 'prodi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'nim' => 'required|unique:mahasiswa',
            'doswal_id' => 'required|exists:dosen,id',
            'prodi_id' => 'required|exists:prodi,id',
            'angkatan' => 'required|digits:4',
            'alamat' => 'required',
            'no_telp' => 'required',
            'gender' => 'required|in:L,P',
            'religion' => 'required',
            'tahun_akademik' => 'required|digits:4',
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'mahasiswa',
            'status' => 'aktif'
        ]);

        // Create mahasiswa
        Mahasiswa::create([
            'user_id' => $user->id,
            'doswal_id' => $request->doswal_id,
            'prodi_id' => $request->prodi_id,
            'nim' => $request->nim,
            'angkatan' => $request->angkatan,
            'alamat' => $request->alamat,
            'no_telp' => $request->no_telp,
            'gender' => $request->gender,
            'religion' => $request->religion,
            'tahun_akademik' => $request->tahun_akademik,
        ]);

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function show(Mahasiswa $mahasiswa)
    {
        return view('admin.mahasiswa.show', compact('mahasiswa'));
    }

    public function edit(Mahasiswa $mahasiswa)
    {
        $dosen = Dosen::all();
        $prodi = Prodi::all();
        return view('admin.mahasiswa.edit', compact('mahasiswa', 'dosen', 'prodi'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $mahasiswa->user_id,
            'nim' => 'required|unique:mahasiswa,nim,' . $mahasiswa->id,
            'doswal_id' => 'required|exists:dosen,id',
            'prodi_id' => 'required|exists:prodi,id',
            'angkatan' => 'required|digits:4',
            'alamat' => 'required',
            'no_telp' => 'required',
            'gender' => 'required|in:L,P',
            'religion' => 'required',
            'tahun_akademik' => 'required|digits:4',
        ]);

        // Update user
        $mahasiswa->user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update mahasiswa
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

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Mahasiswa berhasil diupdate.');
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        $mahasiswa->user->delete();
        $mahasiswa->delete();

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Mahasiswa berhasil dihapus.');
    }
}

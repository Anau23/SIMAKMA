<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\Dosen;
use App\Models\Prodi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Mahasiswa::with(['user', 'dosen', 'prodi']);
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

        $mahasiswa = $query->orderBy('nim')->paginate(10);

        return view('admin.mahasiswa.index', compact('mahasiswa', 'search'));
    }

    public function create()
    {
        $dosen = Dosen::all();
        $prodi = Prodi::all();
        return view('admin.mahasiswa.create', compact('dosen', 'prodi'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'nim' => 'required|unique:mahasiswas',
            'doswal_id' => 'required|exists:dosens,id',
            'prodi_id' => 'required|exists:prodis,id',
            'angkatan' => 'required',
            'alamat' => 'required',
            'no_telp' => 'required',
            'gender' => 'required|in:L,P',
            'religion' => 'required',
            'tahun_akademik' => 'required|digits:4',
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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'mahasiswa',
            'status' => 'aktif'
        ]);

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

    public function edit(Mahasiswa $mahasiswa)
    {
        $dosen = Dosen::all();
        $prodi = Prodi::all();
        return view('admin.mahasiswa.edit', compact('mahasiswa', 'dosen', 'prodi'));
    }

    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $mahasiswa->user_id,
            'nim' => 'required|unique:mahasiswas,nim,' . $mahasiswa->id,
            'doswal_id' => 'required|exists:dosens,id',
            'prodi_id' => 'required|exists:prodis,id',
            'angkatan' => 'required',
            'alamat' => 'required',
            'no_telp' => 'required',
            'gender' => 'required|in:L,P',
            'religion' => 'required',
            'tahun_akademik' => 'required|digits:4',
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

        $mahasiswa->user->update([
            'name' => $request->name,
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

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Mahasiswa berhasil diupdate.');
    }

    public function destroy(Mahasiswa $mahasiswa)
    {
        $mahasiswa->user->delete();
        $mahasiswa->delete();

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Mahasiswa berhasil dihapus.');
    }
    public function biodata()
    {
        $mahasiswa = Mahasiswa::with(['user', 'dosen.user', 'prodi'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('mahasiswa.biodata.index', compact('mahasiswa'));
    }
}

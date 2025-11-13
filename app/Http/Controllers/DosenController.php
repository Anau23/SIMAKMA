<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\User;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DosenController extends Controller
{
    public function index()
    {
        $dosen = Dosen::with(['user', 'prodi'])
            ->when(request('search'), function ($q) {
                $q->where('name', 'like', '%' . request('search') . '%')
                    ->orWhere('nip', 'like', '%' . request('search') . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();
        return view('admin.dosen.index', compact('dosen'));
    }

    public function create()
    {
        $prodi = Prodi::all();
        return view('admin.dosen.create', compact('prodi'));
    }

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

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'dosen',
            'status' => 'aktif'
        ]);

        // Create dosen
        Dosen::create([
            'user_id' => $user->id,
            'prodi_id' => $request->prodi_id,
            'name' => $request->name,
            'nip' => $request->nip,
            'alamat' => $request->alamat,
            'gender' => $request->gender,
            'religion' => $request->religion,
        ]);

        return redirect()->route('admin.dosen.index')->with('success', 'Dosen berhasil ditambahkan.');
    }

    public function show(Dosen $dosen)
    {
        return view('admin.dosen.show', compact('dosen'));
    }

    public function edit(Dosen $dosen)
    {
        $prodi = Prodi::all();
        return view('admin.dosen.edit', compact('dosen', 'prodi'));
    }

    public function update(Request $request, Dosen $dosen)
    {
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

        // Update user
        $dosen->user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update dosen
        $dosen->update($request->only([
            'prodi_id',
            'nip',
            'alamat',
            'gender',
            'religion'
        ]));

        return redirect()->route('admin.dosen.index')->with('success', 'Dosen berhasil diupdate.');
    }

    public function destroy(Dosen $dosen)
    {
        $dosen->user->delete();
        $dosen->delete();

        return redirect()->route('admin.dosen.index')->with('success', 'Dosen berhasil dihapus.');
    }
}

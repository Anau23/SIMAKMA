<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JadwalMatkul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JadwalMatkulController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $jadwals = JadwalMatkul::with(['kela', 'matkul'])
            ->when($search, function ($q, $search) {
                return $q->whereHas('kela', fn($q2) => $q2->where('name', 'like', "%$search%"))
                    ->orWhereHas('matkul', fn($q2) => $q2->where('name', 'like', "%$search%"))
                    ->orWhere('hari', 'like', "%$search%");
            })
            ->orderBy('hari')
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'status' => true,
            'data' => $jadwals
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kelas_id' => 'required|exists:kelas,id',
            'matkul_id' => 'required|exists:matkuls,id',
            'hari' => 'required|string|max:50',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = JadwalMatkul::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Jadwal berhasil dibuat',
            'data' => $data
        ], 201);
    }

    public function update(Request $request, JadwalMatkul $jadwal_matkul)
    {
        $validator = Validator::make($request->all(), [
            'kelas_id' => 'required|exists:kelas,id',
            'matkul_id' => 'required|exists:matkuls,id',
            'hari' => 'required|string|max:50',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $jadwal_matkul->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Jadwal berhasil diperbarui',
            'data' => $jadwal_matkul
        ]);
    }

    public function destroy(JadwalMatkul $jadwal_matkul)
    {
        $jadwal_matkul->delete();

        return response()->json([
            'status' => true,
            'message' => 'Jadwal berhasil dihapus'
        ]);
    }
}

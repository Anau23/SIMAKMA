<?php

namespace App\Http\Controllers;

use App\Models\Kh;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class KHSController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::user()->mahasiswa;

        $khs = Kh::where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('tahun_akademik', 'desc')
            ->orderBy('semester', 'desc')
            ->get();
        $statistics = [
            'total_semester' => $khs->count(),
            'highest_ips' => $khs->max('ips') ?? 0,
            'average_ips' => $khs->avg('ips') ?? 0,
            'latest_semester' => $khs->first(),
        ];

        return view('mahasiswa.khs.index', compact('khs', 'statistics'));
    }

    public function create()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        $latestKHS = Kh::where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('tahun_akademik', 'desc')
            ->orderBy('semester', 'desc')
            ->first();

        $nextSemester = $latestKHS ? $latestKHS->semester + 1 : 1;
        $tahunAkademik = date('Y');

        return view('mahasiswa.khs.create', compact('nextSemester', 'tahunAkademik'));
    }

    public function store(Request $request)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        $request->validate([
            'ips' => 'required|numeric|min:0|max:4',
            'khs_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'tahun_akademik' => 'required|digits:4',
            'semester' => 'required|integer|min:1|max:14',
        ]);
        $existingKHS = Kh::where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik', $request->tahun_akademik)
            ->where('semester', $request->semester)
            ->first();

        if ($existingKHS) {
            return redirect()->back()->with('error', 'KHS untuk semester ini sudah ada.');
        }
        if ($request->hasFile('khs_file')) {
            $file = $request->file('khs_file');
            $filename = 'KHS_' . $mahasiswa->nim . '_' . $request->tahun_akademik . '_' . $request->semester . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('khs', $filename, 'public');
        }

        Kh::create([
            'mahasiswa_id' => $mahasiswa->id,
            'ips' => $request->ips,
            'khs_file' => $filePath ?? null,
            'ips_ocr' => $request->ips_ocr, // Optional OCR value
            'tahun_akademik' => $request->tahun_akademik,
            'semester' => $request->semester,
        ]);

        return redirect()->route('mahasiswa.khs.index')
            ->with('success', 'KHS berhasil diupload.');
    }

    public function show(Kh $kh)
    {
        if ($kh->mahasiswa_id != Auth::user()->mahasiswa->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('mahasiswa.khs.show', compact('kh'));
    }

    public function edit(Kh $kh)
    {
        // Check if KHS belongs to current mahasiswa
        if ($kh->mahasiswa_id != Auth::user()->mahasiswa->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('mahasiswa.khs.edit', compact('kh'));
    }

    public function update(Request $request, Kh $kh)
    {
        if ($kh->mahasiswa_id != Auth::user()->mahasiswa->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'ips' => 'required|numeric|min:0|max:4',
            'khs_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'tahun_akademik' => 'required|digits:4',
            'semester' => 'required|integer|min:1|max:14',
        ]);
        $existingKHS = Kh::where('mahasiswa_id', $kh->mahasiswa_id)
            ->where('tahun_akademik', $request->tahun_akademik)
            ->where('semester', $request->semester)
            ->where('id', '!=', $kh->id)
            ->first();

        if ($existingKHS) {
            return redirect()->back()->with('error', 'KHS untuk semester ini sudah ada.');
        }

        $data = [
            'ips' => $request->ips,
            'tahun_akademik' => $request->tahun_akademik,
            'semester' => $request->semester,
        ];
        if ($request->hasFile('khs_file')) {
            if ($kh->khs_file && Storage::disk('public')->exists($kh->khs_file)) {
                Storage::disk('public')->delete($kh->khs_file);
            }

            $file = $request->file('khs_file');
            $filename = 'KHS_' . $kh->mahasiswa->nim . '_' . $request->tahun_akademik . '_' . $request->semester . '.' . $file->getClientOriginalExtension();
            $data['khs_file'] = $file->storeAs('khs', $filename, 'public');
        }
        if ($request->has('ips_ocr')) {
            $data['ips_ocr'] = $request->ips_ocr;
        }

        $kh->update($data);

        return redirect()->route('mahasiswa.khs.index')
            ->with('success', 'KHS berhasil diupdate.');
    }

    public function destroy(Kh $kh)
    {
        if ($kh->mahasiswa_id != Auth::user()->mahasiswa->id) {
            abort(403, 'Unauthorized action.');
        }
        if ($kh->khs_file && Storage::disk('public')->exists($kh->khs_file)) {
            Storage::disk('public')->delete($kh->khs_file);
        }

        $kh->delete();

        return redirect()->route('mahasiswa.khs.index')
            ->with('success', 'KHS berhasil dihapus.');
    }

    public function download(Kh $kh)
    {
        if ($kh->mahasiswa_id != Auth::user()->mahasiswa->id) {
            abort(403, 'Unauthorized action.');
        }

        if (!$kh->khs_file || !Storage::disk('public')->exists($kh->khs_file)) {
            return redirect()->back()->with('error', 'File KHS tidak ditemukan.');
        }

        return Storage::disk('public')->download($kh->khs_file);
    }
}

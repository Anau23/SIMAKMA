<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kh;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Intervention\Image\Facades\Image;

class KHSController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        $khs = Kh::where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('tahun_akademik', 'desc')
            ->orderBy('semester', 'desc')
            ->paginate(10);

        return view('mahasiswa.khs.index', compact('khs'));
    }

    public function create()
    {
        $mahasiswa = Auth::user()->mahasiswas;
        return view('mahasiswa.khs.create', compact('mahasiswa'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ips' => 'required|numeric|min:0|max:4',
            'khs_file' => [
                'required',
                'image',
                'mimes:png,jpg,jpeg',
                'max:5120',
            ],
        ], [
            'khs_file.image' => 'File harus berupa gambar.',
            'khs_file.mimes' => 'File harus berformat PNG, JPG, atau JPEG.',
            'khs_file.max' => 'Ukuran file maksimal 5MB.',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $errorMessage = "Upload KHS gagal.<br><ul>";
            foreach ($errors as $error) {
                $errorMessage .= "<li>$error</li>";
            }
            $errorMessage .= "</ul>";
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
        $mahasiswa = Auth::user()->mahasiswas;
        $file = $request->file('khs_file');
        $extension = $file->getClientOriginalExtension();
        $fileName = time() . '_' . $mahasiswa->nim . '.' . $extension;
        $path = $file->storeAs('khs', $fileName, 'public');
        $fullPath = storage_path('app/public/' . $path);
        $ips_ocr = null;
        try {
            Log::info('Mencoba OCR pada file: ' . $fullPath);
            $tesseract = new TesseractOCR($fullPath);
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $tesseract->executable('C:\\Program Files\\Tesseract-OCR\\tesseract.exe');
            }
            $ocr = $tesseract->lang('eng')->run();
            Log::info('Hasil OCR:', ['ocr_text' => substr($ocr, 0, 500)]);
            if (preg_match('/[0-4]\.[0-9]{2}/', $ocr, $match)) {
                $ips_ocr = $match[0];
            } elseif (preg_match('/[0-4],\d{2}/', $ocr, $match)) {
                $ips_ocr = str_replace(',', '.', $match[0]);
            }

            Log::info('IPS dari OCR:', ['ips_ocr' => $ips_ocr]);
        } catch (\Exception $e) {
            Log::error('OCR Error: ' . $e->getMessage());
            $ips_ocr = null;
        }
        $angkatan = (int) substr($mahasiswa->tahun_akademik, 0, 4);
        $tahunSekarang = date('Y');
        $selisihTahun = $tahunSekarang - $angkatan;
        $semester = $selisihTahun * 2 + (date('n') >= 7 ? 1 : 2);
        $status_verifikasi = 'pending';

        if ($ips_ocr) {
            $selisih = abs((float)$ips_ocr - (float)$request->ips);
            $inputIps = (float)$request->ips;
            $ocrIps = (float)$ips_ocr;
            if ($selisih <= 0.01 && $inputIps <= $ocrIps) {
                $status_verifikasi = 'valid';
            } elseif ($inputIps > $ocrIps) {
                $status_verifikasi = 'invalid';
            } elseif ($selisih > 0.2) {
                $status_verifikasi = 'invalid';
            } else {
                $status_verifikasi = 'pending';
            }
            Log::info('Perbandingan IPS:', [
                'input' => $inputIps,
                'ocr' => $ocrIps,
                'selisih' => $selisih,
                'input_lebih_tinggi' => $inputIps > $ocrIps,
                'status' => $status_verifikasi
            ]);
        }

        Kh::create([
            'mahasiswa_id' => $mahasiswa->id,
            'ips' => $request->ips,
            'khs_file' => $path,
            'ips_ocr' => $ips_ocr,
            'tahun_akademik' => $mahasiswa->tahun_akademik,
            'semester' => $semester,
            'status_verifikasi' => $status_verifikasi
        ]);

        if ($status_verifikasi === 'valid') {
            $msg = 'KHS berhasil diunggah dan telah diverifikasi otomatis ✅';
        } elseif ($status_verifikasi === 'invalid') {
            $msg = 'KHS berhasil diunggah, namun terdeteksi perbedaan IP yang signifikan. Silakan periksa kembali atau hubungi dosen wali. ⚠️';
        } else {
            $msg = 'KHS berhasil diunggah. Menunggu verifikasi manual oleh dosen.';
        }

        return redirect()->route('mahasiswa.krs.index')
            ->with($status_verifikasi === 'invalid' ? 'warning' : 'success', $msg);
    }

    public function destroy($id)
    {
        $khs = Kh::findOrFail($id);
        Storage::disk('public')->delete($khs->khs_file);
        $khs->delete();

        return back()->with('success', 'Data KHS berhasil dihapus.');
    }
}

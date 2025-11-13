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
        $mahasiswa = Auth::user()->mahasiswas->first();
        return view('mahasiswa.khs.create', compact('mahasiswa'));
    }

    public function store(Request $request)
    {
        // Validasi dengan image instead of PDF
        $validator = Validator::make($request->all(), [
            'ips' => 'required|numeric|min:0|max:4',
            'khs_file' => [
                'required',
                'image',
                'mimes:png,jpg,jpeg',
                'max:5120', // 5MB
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

        $mahasiswa = Auth::user()->mahasiswas->first();

        // Simpan file dengan ekstensi yang benar
        $file = $request->file('khs_file');
        $extension = $file->getClientOriginalExtension();
        $fileName = time() . '_' . $mahasiswa->nim . '.' . $extension;
        $path = $file->storeAs('khs', $fileName, 'public');
        $fullPath = storage_path('app/public/' . $path);

        // Jalankan OCR langsung pada image (lebih mudah!)
        $ips_ocr = null;
        try {
            Log::info('Mencoba OCR pada file: ' . $fullPath);

            $tesseract = new TesseractOCR($fullPath);

            // Set path Tesseract untuk Windows
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $tesseract->executable('C:\\Program Files\\Tesseract-OCR\\tesseract.exe');
            }

            $ocr = $tesseract->lang('eng')->run();

            Log::info('Hasil OCR:', ['ocr_text' => substr($ocr, 0, 500)]);

            // Cari pattern IP (0.00 - 4.00)
            // Coba beberapa pattern untuk lebih fleksibel
            if (preg_match('/[0-4]\.[0-9]{2}/', $ocr, $match)) {
                $ips_ocr = $match[0];
            } elseif (preg_match('/[0-4],\d{2}/', $ocr, $match)) {
                // Jika OCR membaca koma instead of titik
                $ips_ocr = str_replace(',', '.', $match[0]);
            }

            Log::info('IPS dari OCR:', ['ips_ocr' => $ips_ocr]);
        } catch (\Exception $e) {
            Log::error('OCR Error: ' . $e->getMessage());
            $ips_ocr = null;
        }

        // Hitung semester otomatis
        $angkatan = (int) substr($mahasiswa->tahun_akademik, 0, 4);
        $tahunSekarang = date('Y');
        $selisihTahun = $tahunSekarang - $angkatan;
        $semester = $selisihTahun * 2 + (date('n') >= 7 ? 1 : 2);

        // Tentukan status verifikasi otomatis
        $status_verifikasi = 'pending'; // Default pending

        if ($ips_ocr) {
            $selisih = abs((float)$ips_ocr - (float)$request->ips);

            // Toleransi 0.01 untuk kesalahan OCR (misal 3.75 terbaca 3.74 atau 3.76)
            // TAPI input harus LEBIH RENDAH atau SAMA dengan OCR
            // Jika input LEBIH TINGGI dari OCR = curiga manipulasi

            $inputIps = (float)$request->ips;
            $ocrIps = (float)$ips_ocr;

            if ($selisih <= 0.01 && $inputIps <= $ocrIps) {
                // Valid: selisih kecil DAN input tidak lebih tinggi dari OCR
                $status_verifikasi = 'valid';
            } elseif ($inputIps > $ocrIps) {
                // Invalid: input lebih tinggi dari yang terdeteksi OCR (curiga manipulasi)
                $status_verifikasi = 'invalid';
            } elseif ($selisih > 0.2) {
                // Invalid: selisih terlalu besar
                $status_verifikasi = 'invalid';
            } else {
                // Pending: selisih sedang, butuh review manual
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

        // Pesan berdasarkan status
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

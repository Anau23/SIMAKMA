<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Kh
 *
 * @property int $id
 * @property int $mahasiswa_id
 * @property float $ips
 * @property string|null $khs_file
 * @property float|null $ips_ocr
 * @property Carbon $tahun_akademik
 * @property int $semester
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Mahasiswa $mahasiswa
 *
 * @package App\Models
 */
class Kh extends Model
{
	protected $table = 'khs';

	protected $casts = [
		'mahasiswa_id' => 'int',
		'ips' => 'float',
		'ips_ocr' => 'float',
		'semester' => 'int'
	];

	protected $fillable = [
		'mahasiswa_id',
		'ips',
		'khs_file',
		'ips_ocr',
		'tahun_akademik',
		'semester',
        'status_verifikasi'
	];

	public function mahasiswa()
	{
		return $this->belongsTo(Mahasiswa::class);
	}
}

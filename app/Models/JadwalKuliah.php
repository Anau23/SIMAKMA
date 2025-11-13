<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class JadwalKuliah
 * 
 * @property int $id
 * @property int $jadwal_matkul_id
 * @property int $mahasiswa_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property JadwalMatkul $jadwal_matkul
 * @property Mahasiswa $mahasiswa
 *
 * @package App\Models
 */
class JadwalKuliah extends Model
{
	protected $table = 'jadwal_kuliah';

	protected $casts = [
		'jadwal_matkul_id' => 'int',
		'mahasiswa_id' => 'int'
	];

	protected $fillable = [
		'jadwal_matkul_id',
		'mahasiswa_id'
	];

	public function jadwal_matkul()
	{
		return $this->belongsTo(JadwalMatkul::class);
	}

	public function mahasiswa()
	{
		return $this->belongsTo(Mahasiswa::class);
	}
}

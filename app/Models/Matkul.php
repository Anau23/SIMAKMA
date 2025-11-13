<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Matkul
 * 
 * @property int $id
 * @property int $prodi_id
 * @property int $dosen_id
 * @property string $kode_mk
 * @property string $name
 * @property int $sks
 * @property int $semester
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Dosen $dosen
 * @property Prodi $prodi
 * @property Collection|JadwalMatkul[] $jadwal_matkuls
 * @property Collection|Kr[] $krs
 *
 * @package App\Models
 */
class Matkul extends Model
{
	protected $table = 'matkuls';

	protected $casts = [
		'prodi_id' => 'int',
		'dosen_id' => 'int',
		'sks' => 'int',
		'semester' => 'int'
	];

	protected $fillable = [
		'prodi_id',
		'dosen_id',
		'kode_mk',
		'name',
		'sks',
		'semester'
	];

	public function dosen()
	{
		return $this->belongsTo(Dosen::class);
	}

	public function prodi()
	{
		return $this->belongsTo(Prodi::class);
	}

	public function jadwal_matkuls()
	{
		return $this->hasMany(JadwalMatkul::class);
	}

	public function krs()
	{
		return $this->hasMany(Kr::class);
	}
}

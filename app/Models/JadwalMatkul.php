<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class JadwalMatkul
 * 
 * @property int $id
 * @property int $kelas_id
 * @property int $matkul_id
 * @property string $hari
 * @property Carbon $jam_mulai
 * @property Carbon $jam_selesai
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Kela $kela
 * @property Matkul $matkul
 * @property Collection|JadwalKuliah[] $jadwal_kuliahs
 *
 * @package App\Models
 */
class JadwalMatkul extends Model
{
	protected $table = 'jadwal_matkuls';

	protected $casts = [
		'kelas_id' => 'int',
		'matkul_id' => 'int',
		'jam_mulai' => 'datetime',
		'jam_selesai' => 'datetime'
	];

	protected $fillable = [
		'kelas_id',
		'matkul_id',
		'hari',
		'jam_mulai',
		'jam_selesai'
	];

	public function kela()
	{
		return $this->belongsTo(Kela::class, 'kelas_id');
	}

	public function matkul()
	{
		return $this->belongsTo(Matkul::class);
	}

	public function jadwal_kuliahs()
	{
		return $this->hasMany(JadwalKuliah::class);
	}
}

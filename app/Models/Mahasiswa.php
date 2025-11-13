<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Mahasiswa
 * 
 * @property int $id
 * @property int $user_id
 * @property int $doswal_id
 * @property int $prodi_id
 * @property Carbon $angkatan
 * @property int $nim
 * @property string $alamat
 * @property string $no_telp
 * @property string $gender
 * @property string $religion
 * @property Carbon $tahun_akademik
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Dosen $dosen
 * @property Prodi $prodi
 * @property User $user
 * @property Collection|JadwalKuliah[] $jadwal_kuliahs
 * @property Collection|Kh[] $khs
 * @property Collection|Kr[] $krs
 *
 * @package App\Models
 */
class Mahasiswa extends Model
{
	protected $table = 'mahasiswas';

	protected $casts = [
		'user_id' => 'int',
		'doswal_id' => 'int',
		'prodi_id' => 'int',
		'angkatan' => 'string',
		'nim' => 'int',
		'tahun_akademik' => 'string'
	];

	protected $fillable = [
		'user_id',
		'doswal_id',
		'prodi_id',
		'angkatan',
		'nim',
		'alamat',
		'no_telp',
		'gender',
		'religion',
		'tahun_akademik'
	];

	public function dosen()
	{
		return $this->belongsTo(Dosen::class, 'doswal_id');
	}

	public function prodi()
	{
		return $this->belongsTo(Prodi::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function jadwal_kuliahs()
	{
		return $this->hasMany(JadwalKuliah::class);
	}

	public function khs()
	{
		return $this->hasMany(Kh::class);
	}

	public function krs()
	{
		return $this->hasMany(Kr::class);
	}
}

<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Dosen
 * 
 * @property int $id
 * @property int $user_id
 * @property int $prodi_id
 * @property string $name
 * @property int $nip
 * @property string $alamat
 * @property string $gender
 * @property string $religion
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Prodi $prodi
 * @property User $user
 * @property Collection|Mahasiswa[] $mahasiswas
 * @property Collection|Matkul[] $matkuls
 *
 * @package App\Models
 */
class Dosen extends Model
{
	protected $table = 'dosens';

	protected $casts = [
		'user_id' => 'int',
		'prodi_id' => 'int',
		'nip' => 'int'
	];

	protected $fillable = [
		'user_id',
		'prodi_id',
		'name',
		'nip',
		'alamat',
		'gender',
		'religion'
	];

	public function prodi()
	{
		return $this->belongsTo(Prodi::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function mahasiswas()
	{
		return $this->hasMany(Mahasiswa::class, 'doswal_id');
	}

	public function matkuls()
	{
		return $this->hasMany(Matkul::class);
	}
}

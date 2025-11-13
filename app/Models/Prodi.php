<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Prodi
 * 
 * @property int $id
 * @property int $fakultas_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Fakulta $fakulta
 * @property Collection|Dosen[] $dosens
 * @property Collection|Mahasiswa[] $mahasiswas
 * @property Collection|Matkul[] $matkuls
 *
 * @package App\Models
 */
class Prodi extends Model
{
	protected $table = 'prodis';

	protected $casts = [
		'fakultas_id' => 'int'
	];

	protected $fillable = [
		'fakultas_id',
		'name'
	];

	public function fakulta()
	{
		return $this->belongsTo(Fakulta::class, 'fakultas_id');
	}

	public function dosens()
	{
		return $this->hasMany(Dosen::class);
	}

	public function mahasiswas()
	{
		return $this->hasMany(Mahasiswa::class);
	}

	public function matkuls()
	{
		return $this->hasMany(Matkul::class);
	}
}

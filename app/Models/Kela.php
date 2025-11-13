<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Kela
 * 
 * @property int $id
 * @property string $name
 * @property string $ruang
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|JadwalMatkul[] $jadwal_matkuls
 *
 * @package App\Models
 */
class Kela extends Model
{
	protected $table = 'kelas';

	protected $fillable = [
		'name',
		'ruang'
	];

	public function jadwal_matkuls()
	{
		return $this->hasMany(JadwalMatkul::class, 'kelas_id');
	}
}

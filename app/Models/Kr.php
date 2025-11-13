<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Kr
 * 
 * @property int $id
 * @property int $mahasiswa_id
 * @property int $matkul_id
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Mahasiswa $mahasiswa
 * @property Matkul $matkul
 *
 * @package App\Models
 */
class Kr extends Model
{
	protected $table = 'krs';

	protected $casts = [
		'mahasiswa_id' => 'int',
		'matkul_id' => 'int'
	];

	protected $fillable = [
		'mahasiswa_id',
		'matkul_id',
		'status'
	];

	public function mahasiswa()
	{
		return $this->belongsTo(Mahasiswa::class);
	}

	public function matkul()
	{
		return $this->belongsTo(Matkul::class);
	}
}

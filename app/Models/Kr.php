<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kr extends Model
{
	protected $table = 'krs';

	protected $fillable = [
		'mahasiswa_id',
		'matkul_id',
		'status'
	];

	protected $casts = [
		'mahasiswa_id' => 'int',
		'matkul_id' => 'int'
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

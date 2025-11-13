<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Fakulta
 * 
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Prodi[] $prodis
 *
 * @package App\Models
 */
class Fakulta extends Model
{
	protected $table = 'fakultas';

	protected $fillable = [
		'name'
	];

	public function prodis()
	{
		return $this->hasMany(Prodi::class, 'fakultas_id');
	}
}

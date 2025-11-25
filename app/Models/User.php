<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
	use Notifiable, HasApiTokens;

	protected $table = 'users';

	protected $casts = [
		'email_verified_at' => 'datetime',
	];

	protected $hidden = [
		'password',
		'remember_token',
	];

	protected $fillable = [
		'name',
		'email',
		'email_verified_at',
		'password',
		'phone',
		'status',
		'role',
		'remember_token',
	];

	public function dosens()
	{
		return $this->hasMany(Dosen::class);
	}

	// public function mahasiswas()
	// {
	// 	return $this->hasMany(Mahasiswa::class);
	// }
	public function mahasiswas()
	{
		return $this->hasOne(Mahasiswa::class, 'user_id');
	}

	public function isAdmin()
	{
		return $this->role === 'admin';
	}

	public function isDosen()
	{
		return $this->role === 'dosen';
	}

	public function isMahasiswa()
	{
		return $this->role === 'mahasiswa';
	}

	public function hasRole($role)
	{
		return $this->role === $role;
	}
}

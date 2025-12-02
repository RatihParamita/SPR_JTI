<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserModel extends Authenticatable implements JWTSubject
{
    use HasFactory;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    protected $table= 'm_user'; //mendefinisikan nama tabel yang akan digunakan
    protected $primaryKey = 'user_id';
    protected $fillable = ['level_id', 'username', 'password', 'created_at', 'updated_at'];
    protected $hidden   = ['password'];

    protected $casts    = ['password' => 'hashed'];

    public function level(): BelongsTo
    {
        return $this->belongsTo(LevelModel::class, 'level_id', 'level_id');
    }

    //mendapatkan nama role
    public function getRoleName(): string
    {
        return $this->level->level_nama;
    }

    //cek apakah user memiliki kode tertentu
    public function hasRole($role): bool
    {
        return $this->level->level_kode == $role;
    }

    //mendapatkan kode role
    public function getRole(): string
    {
        return $this->level->level_kode;
    }

    public function admin()
    {
        return $this->hasOne(AdminModel::class, 'user_id', 'user_id');
    }
    public function dosen()
    {
        return $this->hasOne(DosenModel::class, 'user_id', 'user_id');
    }
    public function tendik()
    {
        return $this->hasOne(TendikModel::class, 'user_id', 'user_id');
    }
    public function mahasiswa()
    {
        return $this->hasOne(MahasiswaModel::class, 'user_id', 'user_id');
    }

    public function jadwal(): HasMany
    {
        return $this->hasMany(JadwalModel::class, 'user_id', 'user_id');
    }
    public function getNamaPembuatAttribute()
    {
        if ($this->dosen) {
            return $this->dosen->dosen_nama;
        } elseif ($this->admin) {
            return $this->admin->admin_nama;
        } elseif ($this->tendik) {
            return $this->tendik->tendik_nama;
        } else {
            return '-';
        }
    }
}

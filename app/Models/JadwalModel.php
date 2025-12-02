<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class JadwalModel extends Model
{
    use HasFactory;
    protected $table= 't_jadwal'; //mendefinisikan nama tabel yang akan digunakan
    protected $primaryKey = 'jadwal_id';
    protected $fillable = ['user_id','jadwal_nama','jadwal_tgl','jadwal_jam_mulai','jadwal_jam_selesai','jadwal_jumPes','created_at','updated_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    public function ruangans(): BelongsToMany
    {
        // Tabel pivot: t_jadwal_ruangan
        // FK di pivot untuk Jadwal: jadwal_id
        // FK di pivot untuk Ruangan: ruangan_id
        return $this->belongsToMany(
            RuanganModel::class,
            't_jadwal_ruangan',
            'jadwal_id',
            'ruangan_id'
        )->withTimestamps();
    }
}

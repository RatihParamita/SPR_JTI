<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RuanganModel extends Model
{
    use HasFactory;
    protected $table= 'm_ruangan'; //mendefinisikan nama tabel yang akan digunakan
    protected $primaryKey = 'ruangan_id';
    protected $fillable = ['ruangan_kode','ruangan_nama', 'ruangan_fasilitas', 'ruangan_kuota', 'created_at','updated_at'];

    public function jadwal(): BelongsToMany
    {
        // Tabel pivot: t_jadwal_ruangan
        // FK di pivot untuk Ruangan: ruangan_id
        // FK di pivot untuk Jadwal: jadwal_id
        return $this->belongsToMany(
            JadwalModel::class,
            't_jadwal_ruangan',
            'ruangan_id',
            'jadwal_id'
        )->withTimestamps();
    }
}

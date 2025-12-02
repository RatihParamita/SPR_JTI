<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MahasiswaModel extends Model
{
    use HasFactory;
    protected $table= 'm_mahasiswa'; //mendefinisikan nama tabel yang akan digunakan
    protected $primaryKey = 'mahasiswa_id';
    protected $fillable = ['user_id', 'prodi_id', 'kelas_id', 'mahasiswa_nama','mahasiswa_nim','mahasiswa_noHp','created_at','updated_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
    
    public function prodi(): BelongsTo
    {
        return $this->belongsTo(ProdiModel::class, 'prodi_id', 'prodi_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(KelasModel::class, 'kelas_id', 'kelas_id');
    }
}

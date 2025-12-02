<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DosenModel extends Model
{
    use HasFactory;
    protected $table= 'm_dosen'; //mendefinisikan nama tabel yang akan digunakan
    protected $primaryKey = 'dosen_id';
    protected $fillable = ['user_id', 'prodi_id', 'dosen_nama','dosen_nidn','dosen_noHp','created_at','updated_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
    
    public function prodi(): BelongsTo
    {
        return $this->belongsTo(ProdiModel::class, 'prodi_id', 'prodi_id');
    }
}

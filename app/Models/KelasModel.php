<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KelasModel extends Model
{
    use HasFactory;
    protected $table= 'm_kelas'; //mendefinisikan nama tabel yang akan digunakan
    protected $primaryKey = 'kelas_id';
    protected $fillable = ['prodi_id','kelas_nama','created_at','updated_at'];

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(ProdiModel::class, 'prodi_id', 'prodi_id');
    }

    public function mahasiswa(): HasMany
    {
        return $this->hasMany(MahasiswaModel::class, 'kelas_id', 'kelas_id');
    }
}

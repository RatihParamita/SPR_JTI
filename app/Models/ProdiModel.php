<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProdiModel extends Model
{
    use HasFactory;
    protected $table= 'm_prodi'; //mendefinisikan nama tabel yang akan digunakan
    protected $primaryKey = 'prodi_id';
    protected $fillable = ['prodi_kode','prodi_nama','created_at','updated_at'];

    public function kelas(): HasMany
    {
        return $this->hasMany(KelasModel::class, 'prodi_id', 'prodi_id');
    }
}

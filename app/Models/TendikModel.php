<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TendikModel extends Model
{
    use HasFactory;
    protected $table= 'm_tendik'; //mendefinisikan nama tabel yang akan digunakan
    protected $primaryKey = 'tendik_id';
    protected $fillable = ['user_id', 'tendik_nama','tendik_nidn','tendik_noHp','created_at','updated_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
    
    
}

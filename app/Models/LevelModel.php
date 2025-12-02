<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelModel extends Model
{
    use HasFactory;
    protected $table= 'm_level'; //mendefinisikan nama tabel yang akan digunakan
    protected $primaryKey = 'level_id';
    protected $fillable = ['level_kode','level_nama'];
}

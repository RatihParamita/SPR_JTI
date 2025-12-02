<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormulirModel extends Model
{
    use HasFactory;
    protected $table= 'm_formulir'; //mendefinisikan nama tabel yang akan digunakan
    protected $primaryKey = 'formulir_id';
    protected $fillable = ['formulir_path', 'last_uploaded_at'];
}

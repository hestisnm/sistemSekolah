<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class konten extends Model
{
    use HasFactory; 

    //
    protected $table = 'datakonten';
    protected $fillable = ['judul', 'isi', 'detil'];
}

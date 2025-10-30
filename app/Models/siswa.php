<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class siswa extends Model
{
    use HasFactory;
    
    protected $table = 'datasiswa';
    protected $primaryKey = 'idsiswa';
    
    protected $fillable = [
        'id',
        'nama',
        'tb',
        'bb'
    ];
    
    public function admin()
    {
        return $this->belongsTo(\App\Models\admin::class, 'id');
    }
    
    public function kelas()
    {
        return $this->hasOne(kelas::class, 'idsiswa');
    }

}

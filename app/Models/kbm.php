<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kbm extends Model
{
    use HasFactory;
    //
    protected $table = 'datakbm';
    protected $fillable = [
        'idguru',
        'idwalas',
        'hari',
        'mulai',
        'selesai',
    ];
    public function guru()
    {
        return $this->belongsTo(guru::class);
    }
    public function walas()
    {
        return $this->belongsTo(walas::class);
    }
    
}

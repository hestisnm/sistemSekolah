<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kbm extends Model
{
    use HasFactory;

    protected $table = 'datakbm';

    protected $fillable = ['idguru','idmapel','idwalas','hari','mulai','selesai'];

   public function walas()
{
    return $this->belongsTo(walas::class, 'idwalas', 'idwalas');
}

  public function guru()
{
    return $this->belongsTo(guru::class, 'idguru', 'idguru');
}


}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class guru extends Model
{
    use HasFactory;
    //
    protected $table = 'dataguru';
    protected $primaryKey = 'idguru';
    protected $fillable = ['id', 'nama', 'mapel'];

    public function admin()
    {
        return $this->belongsTo(\App\Models\admin::class, 'id');
    }

    public function walas()
    {
        return $this->hasOne(\App\Models\walas::class, 'idguru');
    }
    public function kbm()
    {
        return $this->hasMany(kbm::class);
    }
}

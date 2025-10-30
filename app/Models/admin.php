<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class admin extends Model
{
    use HasFactory;
    //
    protected $table = 'dataadmin';
    protected $fillable = [
        'username',
        'password',
        'role'
    ];
    protected $primaryKey = 'id';
    public function siswa()
    {
        return $this->hasOne('App\Models\siswa', 'id');
    }

    public function guru()
    {
        return $this->hasOne('App\Models\guru', 'id');
    }
}

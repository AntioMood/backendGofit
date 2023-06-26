<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class role extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_role';
    protected $keyType = 'string';
    protected $fillable = [
        'id_role',
        'nama_role'
    ];

    public function pegawai(){
        return $this->hasMany(role::class, 'id_role', 'id');
    }
}

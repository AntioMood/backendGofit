<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pegawai extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_pegawai';
    protected $keyType = 'string';
    protected $fillable = [
        'id_pegawai',
        'id_role',
        'nama_pegawai',
        'jenis_kelamin',
        'alamat',
        'email',
        'password',
        'no_telp'
    ];

    public function aktivasi(){
        return $this->hasMany(pegawai::class, 'id_pegawai', 'id');
    }

    public function depositU(){
        return $this->hasMany(pegawai::class, 'id_pegawai', 'id');
    }

    public function depositK(){
        return $this->hasMany(pegawai::class, 'id_pegawai', 'id');
    }

    public function role(){
        return $this->belongsTo(role::class, 'id_role');
    }
}

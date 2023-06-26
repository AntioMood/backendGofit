<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kelas extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_kelas';
    protected $keyType = 'string';
    protected $fillable = [
        'id_kelas',
        'nama_kelas',
        'kuantitas_kelas',
        'harga'
    ];

    public function jadwalU(){
        return $this->hasMany(kelas::class, 'id_kelas', 'id');
    }

    public function jadwalH(){
        return $this->hasMany(kelas::class, 'id_kelas', 'id');
    }

    public function deposit_kelas(){
        return $this->hasMany(kelas::class, 'id_kelas', 'id');
    }

    public function booking_kelas(){
        return $this->hasMany(kelas::class, 'id_kelas', 'id');
    }
}

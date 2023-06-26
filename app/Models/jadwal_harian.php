<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class jadwal_harian extends Model
{
    use HasFactory;
    protected $table = 'jadwal_harians';
    protected $primaryKey = 'id_jadwalH';
    protected $keyType = 'string';
    protected $fillable = [
        'id_jadwalH',
        'id_jadwalU',
        'id_kelas',
        'id_instruktur',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'tanggal',
        'status'
    ];
    
    public function kelas(){
        return $this->belongsTo(kelas::class, 'id_kelas');
    }

    public function instruktur(){
        return $this->belongsTo(instruktur::class, 'id_instruktur');
    }

    public function perizinan(){
        return $this->hasMany(jadwal_harian::class, 'id_jadwalH', 'id');
    }

    public function booking_kelas(){
        return $this->hasMany(jadwal_harian::class, 'id_jadwalH', 'id');
    }

    public function presensiI(){
        return $this->hasMany(jadwal_harian::class, 'id_jadwalH', 'id');
    }
}

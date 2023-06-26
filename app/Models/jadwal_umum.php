<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class jadwal_umum extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_jadwalU';
    protected $keyType = 'string';
    protected $fillable = [
        'id_jadwalU',
        'id_instruktur',
        'id_kelas',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    public function kelas(){
        return $this->belongsTo(kelas::class, 'id_kelas');
    }

    public function instruktur(){
        return $this->belongsTo(instruktur::class, 'id_instruktur');
    }
}

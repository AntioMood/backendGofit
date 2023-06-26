<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class presensi_instruktur extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_presensi_instruktur';
    protected $keyType = 'string';
    protected $table = "presensi_instrukturs";

    protected $fillable = [
        'id_presensi_instruktur',
        'id_jadwalH',
        'jam_mulai',
        'jam_selesai',
        'tgl_presensi',
    ];

    public function jadwalH(){
        return $this->belongsTo(jadwal_harian::class, 'id_jadwalH');
    }
}

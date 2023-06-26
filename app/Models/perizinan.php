<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class perizinan extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_perizinan';
    protected $keyType = 'string';
    protected $table = "perizinans";

    protected $fillable = [
        'id_perizinan',
        'id_instruktur',
        'id_jadwalH',
        'id_instruktur_pengganti',
        'status',
        'keterangan',
        'tgl_izin',
    ];

    public function instruktur(){
        return $this->belongsTo(instruktur::class, 'id_instruktur');
    }

    public function jadwalH(){
        return $this->belongsTo(jadwal_harian::class, 'id_jadwalH');
    }
}

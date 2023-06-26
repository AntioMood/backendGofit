<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class booking_kelas extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_booking';
    protected $keyType = 'string';
    protected $table = "booking_kelas";
    protected $fillable = [
        'id_booking',
        'noStrukBK',
        'id_member',
        'id_jadwalH',
        'tgl_booking',
        'jenis_pembayaran',
        'status'
    ];

    public function member(){
        return $this->belongsTo(member::class, 'id_member');
    }

    public function jadwal_harian(){
        return $this->belongsTo(jadwal_harian::class, 'id_jadwalH');
    }

    public function kelas(){
        return $this->belongsTo(kelas::class, 'id_kelas');
    }
}

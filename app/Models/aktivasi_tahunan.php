<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class aktivasi_tahunan extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_aktivasi';
    protected $keyType = 'string';
    protected $table = "aktivasi_tahunans";
    protected $fillable = [
        'id_aktivasi',
        'no_strukA',
        'id_pegawai',
        'id_member',
        'tgl_transaksi',
        'tgl_exp'
    ];

    public function member(){
        return $this->belongsTo(member::class, 'id_member');
    }

    public function pegawai(){
        return $this->belongsTo(pegawai::class, 'id_pegawai');
    }
}

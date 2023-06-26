<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transaksi_deposit_kelas extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_TdepoK';
    protected $keyType = 'string';
    protected $table = 'transaksi_deposit_kelas';
    protected $fillable = [
        'id_TdepoK',
        'no_strukK',
        'id_pegawai',
        'id_member',
        'id_kelas',
        'id_promoK',
        'tgl_transaksi',
        'tgl_exp',
        'depoK',
        'totalBayar',
        'totalDepoK',
        'bonus'
    ]; 

    public function member(){
        return $this->belongsTo(member::class, 'id_member');
    }

    public function pegawai(){
        return $this->belongsTo(pegawai::class, 'id_pegawai');
    }

    public function promoK(){
        return $this->belongsTo(promoKelas::class, 'id_promoK');
    }

    public function kelas(){
        return $this->belongsTo(kelas::class, 'id_kelas');
    }
}

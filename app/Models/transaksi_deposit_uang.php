<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transaksi_deposit_uang extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_TdepoU';
    protected $keyType = 'string';
    protected $table = "transaksi_deposit_uangs";
    protected $fillable = [
        'id_TdepoU',
        'no_strukU',
        'id_pegawai',
        'id_member',
        'id_promo',
        'tgl_transaksi',
        'depoU',
        'totalDepoU',
        'bonus',
        'sisa',
    ]; 

    public function member(){
        return $this->belongsTo(member::class, 'id_member');
    }

    public function pegawai(){
        return $this->belongsTo(pegawai::class, 'id_pegawai');
    }

    public function promo(){
        return $this->belongsTo(promo::class, 'id_promo');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class booking_gym extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_booking_gym';
    protected $keyType = 'string';
    protected $table = "booking_gyms";
    protected $fillable = [
        'id_booking_gym',
        'noStrukBG',
        'id_member',
        'id_sesi',
        'tgl_booking',
    ];

    public function member(){
        return $this->belongsTo(member::class, 'id_member');
    }

    public function sesi_gym(){
        return $this->belongsTo(sesi_gym::class, 'id_sesi');
    }
}

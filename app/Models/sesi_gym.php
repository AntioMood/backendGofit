<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sesi_gym extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_sesi';
    protected $keyType = 'string';
    protected $table = "sesi_gyms";
    protected $fillable = [
        'id_sesi',
        'jam_mulai',
        'jam_selesai',
        'kapasitas',
    ];

    public function booking_gym(){
        return $this->hasMany(sesi_gym::class, 'id_sesi', 'id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class member extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id_member';
    protected $keyType = 'string';
    protected $table = "members";
    protected $fillable = [
        'id_member',
        'nama_member',
        'tgl_lahir',
        'alamat',
        'email',
        'password',
        'no_telp',
        'deposit_uang',
        'status',
        'tgl_pembuatan',
        'tgl_exp',
        'jenis_kelamin'
    ];

    public function at(){
        return $this->hasMany(member::class, 'id_member', 'id');
    }

    public function depoU(){
        return $this->hasMany(member::class, 'id_member', 'id');
    }

    public function depoK(){
        return $this->hasMany(member::class, 'id_member', 'id');
    }

    public function bookingKelas(){
        return $this->hasMany(member::class, 'id_member', 'id');
    }

    public function bookingGym(){
        return $this->hasMany(member::class, 'id_member', 'id');
    }
}

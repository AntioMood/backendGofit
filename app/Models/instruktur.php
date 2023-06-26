<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class instruktur extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_instruktur';
    protected $keyType = 'string';
    protected $table = "instrukturs";
    protected $fillable = [
        'id_instruktur',
        'nama_instruktur',
        'jenis_kelamin',
        'tgl_lahir',
        'no_telp',
        'email',
        'pass'
    ];

    public function jadwalUmum(){
        return $this->hasMany(instruktur::class, 'id_instruktur', 'id');
    }

    public function jadwalHarian(){
        return $this->hasMany(instruktur::class, 'id_instruktur', 'id');
    }

    public function perizinan(){
        return $this->hasMany(instruktur::class, 'id_instruktur', 'id');
    }
}

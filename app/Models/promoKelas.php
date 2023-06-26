<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class promoKelas extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_promoK';
    protected $keyType = 'string';
    protected $table = "promo_kelas";
    protected $fillable = [
        'id_promo',
        'syarat',
        'bonus',
    ];

    public function depositKelas(){
        return $this->hasMany(promoKelas::class, 'id_promoK', 'id');
    }
}

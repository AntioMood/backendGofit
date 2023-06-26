<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class promo extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_promo';
    protected $keyType = 'string';
    protected $table = "promos";
    protected $fillable = [
        'id_promo',
        'syarat',
        'bonus',
    ];

    public function depositUang(){
        return $this->hasMany(promo::class, 'id_promo', 'id');
    }
}

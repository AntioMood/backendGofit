<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class deposit_kelas extends Model
{
    use HasFactory;
    protected $primaryKey = null;
    public $increment = false;
    protected $table = "deposit_kelas";
    protected $fillable = [
        'id_member',
        'id_kelas',
        'deposit_kelas',
        'tgl_exp'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelKarir extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_level',
        'minimal_RO_QR',
        'angka_deviden',
        'jumlah_mitra_level_ini',
    ];
}

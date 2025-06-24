<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produks';

    protected $fillable = [
        'paket',
        'nama',
        'harga_stokis',
        'harga_member',
        'gambar',
        'deskripsi',
        'status_aktif',
    ];
}

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

    /**
     * Relasi dengan stok produk
     */
    public function produkStoks()
    {
        return $this->hasMany(ProdukStok::class);
    }

    /**
     * Relasi dengan user yang memiliki stok produk ini
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'produk_stoks')
            ->withPivot('stok')
            ->withTimestamps();
    }
}

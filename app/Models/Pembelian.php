<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $fillable = [
        'tgl_beli',
        'user_id',
        'beli_dari',
        'tujuan_beli',
        'nama_penerima',
        'no_telp',
        'alamat_tujuan',
        'total_beli',
        'total_bonus',
        'status_pembelian',
        'images',
        'jumlah_poin_qr',
        'kategori_pembelian',
        'cashback',
        'total_cashback',
        'id_sponsor',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'beli_dari');
    }
   

    public function sponsor()
    {
        return $this->belongsTo(User::class, 'id_sponsor');
    }

    public function details()
    {
        return $this->hasMany(PembelianDetail::class);
    }

    public function pembelianBonuses()
    {
        return $this->hasMany(PembelianBonus::class);
    }
}

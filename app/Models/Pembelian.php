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
        'jumlah_poin_qr',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'beli_dari');
    }

    public function details()
    {
        return $this->hasMany(PembelianDetail::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'pembelian_id',
        'produk_id',
        'nama_produk',
        'paket',
        'jml_beli',
        'harga_beli',
        'nominal_bonus_sponsor',
        'nominal_bonus_generasi',
        'user_id_get_bonus_sponsor',
        'group_user_id_get_bonus_generasi',
        'cashback',
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function bonusSponsorUser()
    {
        return $this->belongsTo(User::class, 'user_id_get_bonus_sponsor');
    }
}

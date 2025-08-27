<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AktivasiPin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pin',
        'pembelian_detail_id',
        'produk_id',
        'is_accept',
    ];

    protected $casts = [
        'is_accept' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pembelianDetail()
    {
        return $this->belongsTo(PembelianDetail::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}

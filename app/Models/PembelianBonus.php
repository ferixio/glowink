<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianBonus extends Model
{
    use HasFactory;
    protected $fillable = [
        'pembelian_id',
        'user_id',
        'keterangan',
        'tipe',
        'aktivasi_pin_id',
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function aktivasiPin()
    {
        return $this->belongsTo(AktivasiPin::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

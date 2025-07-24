<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailDevidenBulanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'deviden_bulanan_id',
        'nama_level',
        'jumlah_mitra',
        'jumlah_mitra_transaksi',
        'omzet_ro_qr',
        'angka_deviden',
        'nominal_deviden_bulanan',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevidenHarian extends Model
{
    use HasFactory;

    protected $fillable = [
        'omzet_aktivasi',
        'omzet_ro_basic',
        'total_member',
        'deviden_diterima',
        'tanggal_deviden',
    ];
}

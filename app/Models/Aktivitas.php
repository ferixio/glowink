<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aktivitas extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'judul',
        'keterangan',
        'status',
        'nominal',
        'tipe',
    ];

    // Pastikan timestamps aktif
    public $timestamps = true;
}

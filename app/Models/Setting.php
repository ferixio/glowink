<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'keterangan',
        'bank_name',
        'bank_atas_nama',
        'no_rek',
        'email',
        'alamat',
        'telepon',
        'logo',
        'val',
    ];
}

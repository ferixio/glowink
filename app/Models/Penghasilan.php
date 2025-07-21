<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penghasilan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tgl_dapat_bonus',
        'keterangan',
        'nominal_bonus',
        'kategori_bonus',
        'status_qr',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevidenBulanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal_input',
        'start_date',
        'end_date',
        'total_deviden_bulanan',
    ];
}

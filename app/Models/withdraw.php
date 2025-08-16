<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tgl_withdraw',
        'nominal',
        'status',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tgl_withdraw' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

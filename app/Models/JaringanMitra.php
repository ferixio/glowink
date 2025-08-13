<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JaringanMitra extends Model
{
    use HasFactory;

    protected $table = 'jaringan_mitras';
    protected $fillable = [
        'user_id',
        'sponsor_id',
        'level',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sponsor()
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'group_sponsor' => 'array',
        'password' => 'hashed',
        'tgl_daftar' => 'date',
        'saldo_penghasilan' => 'decimal:2',
        'poin_reward' => 'decimal:2',
        'next_poin_karir' => 'decimal:2',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->isAdmin;
        }

        return !$this->isAdmin;
    }

    public function getFilamentName(): string
    {
        return $this->nama;
    }

    /**
     * Relasi sponsor untuk kebutuhan Filament Select::relationship('sponsor', 'nama')
     */
    public function sponsor()
    {
        return $this->belongsTo(User::class, 'id_sponsor');
    }

    /**
     * Relasi untuk mendapatkan data sponsor dengan eager loading
     */
    public function sponsorWithMitra()
    {
        return $this->belongsTo(User::class, 'id_sponsor')->select('id', 'id_mitra', 'nama');
    }

    /**
     * Relasi dengan stok produk yang dimiliki user
     */
    public function produkStoks()
    {
        return $this->hasMany(ProdukStok::class);
    }

    /**
     * Relasi dengan produk yang dimiliki user (melalui ProdukStok)
     */
    public function produk()
    {
        return $this->belongsToMany(Produk::class, 'produk_stoks')
            ->withPivot('stok')
            ->withTimestamps();
    }

    public function jaringanDownline()
    {
        return $this->hasMany(JaringanMitra::class, 'sponsor_id');
    }

    public function jaringanUpline()
    {
        return $this->hasMany(JaringanMitra::class, 'user_id');
    }

}

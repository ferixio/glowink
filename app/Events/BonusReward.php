<?php

namespace App\Events;

use App\Models\Pembelian;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BonusReward
{
    use Dispatchable, SerializesModels;

    public $pembelian;
    public $isUserUpdateQR; 

    public function __construct(Pembelian $pembelian, $isUserUpdateQR = false)
    {
        $this->pembelian = $pembelian;
        $this->isUserUpdateQR = $isUserUpdateQR;
    }
}

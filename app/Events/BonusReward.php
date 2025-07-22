<?php

namespace App\Events;

use App\Models\Pembelian;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BonusReward
{
    use Dispatchable, SerializesModels;

    public $pembelian;

    public function __construct(Pembelian $pembelian)
    {
        $this->pembelian = $pembelian;
    }
}

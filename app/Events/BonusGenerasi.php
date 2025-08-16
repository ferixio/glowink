<?php

namespace App\Events;

use App\Models\Pembelian;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BonusGenerasi
{
    use Dispatchable, SerializesModels;

    public $pembelian;
    public $isMemberAktivasi;

    public function __construct(Pembelian $pembelian, bool $isMemberAktivasi = false)
    {
        $this->pembelian = $pembelian;
        $this->isMemberAktivasi = $isMemberAktivasi;
    }
}

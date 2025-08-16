<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SpillOverBonusBulanan
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $pembelian_id;
    public $kategori_pembelian;
    public $pembelian;

    /**
     * Create a new event instance.
     */
    public function __construct($user_id, $pembelian_id = null, $kategori_pembelian = null, $pembelian = null)
    {
        $this->user_id = $user_id;
        $this->pembelian_id = $pembelian_id;
        $this->kategori_pembelian = $kategori_pembelian;
        $this->pembelian = $pembelian;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}

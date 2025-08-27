<?php

namespace App\Events;

use App\Models\AktivasiPin;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BonusAktivasiPin
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $aktivasiPin;

    /**
     * Create a new event instance.
     */
    public function __construct(AktivasiPin $aktivasiPin)
    {
        $this->aktivasiPin = $aktivasiPin;
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

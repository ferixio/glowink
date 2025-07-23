<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChangeLevelUser
{
    use Dispatchable, SerializesModels;

    public $user;
    public $newLevel;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\User  $user
     * @param  mixed  $newLevel
     * @return void
     */
    public function __construct(User $user, $newLevel)
    {
        $this->user = $user;
        $this->newLevel = $newLevel;
    }
}

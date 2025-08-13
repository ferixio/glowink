<?php

namespace App\Services;

use App\Events\UserCreated;
use App\Models\User;

class JaringanMitraService
{
    /**
     * Trigger event untuk membuat jaringan mitra
     *
     * @param User $user User yang baru dibuat
     * @param int|null $sponsorId ID sponsor (bisa null jika tidak ada sponsor)
     * @return void
     */
    public static function createJaringanMitra(User $user, $sponsorId = null)
    {
        // Dispatch event UserCreated
        event(new UserCreated($user, $sponsorId));
    }

    /**
     * Trigger event untuk membuat jaringan mitra (non-static method)
     *
     * @param User $user User yang baru dibuat
     * @param int|null $sponsorId ID sponsor (bisa null jika tidak ada sponsor)
     * @return void
     */
    public function triggerJaringanMitra(User $user, $sponsorId = null)
    {
        self::createJaringanMitra($user, $sponsorId);
    }
}

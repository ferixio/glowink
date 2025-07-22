<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        // Only set id_mitra if not already set
        if (empty($user->id_mitra)) {
            $prefix = 'G'; // Default code for mitra
            $date = now()->format('Ymd');
            $id = $user->id;
            $idMitra = $prefix . $date . $id;

            // Ensure uniqueness (should be unique by id, but double check)
            $exists = User::where('id_mitra', $idMitra)->exists();
            if ($exists) {
                // If exists, append a random string (should not happen)
                $idMitra .= strtoupper(uniqid());
            }

            $user->id_mitra = $idMitra;
            $user->save();
        }
    }
}

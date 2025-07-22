<?php

namespace App\Livewire\User;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardUser extends Component
{
    public function render()
    {
        $user = Auth::user();
        return view('livewire.user.dashboard-user', compact('user'));
    }
}

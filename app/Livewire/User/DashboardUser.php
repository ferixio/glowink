<?php

namespace App\Livewire\User;

use App\Models\DevidenHarian;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardUser extends Component
{
    public function render()
    {
        $user = Auth::user();
        $devidenHarian = DevidenHarian::where('tanggal_deviden', now()->format('Y-m-d'))->first();
        $totalDevidenBulanLalu = DevidenHarian::where('tanggal_deviden', now()->subMonth()->format('Y-m-d'))->sum('deviden_diterima');
        $totalPenerimaDevidenHarian = DevidenHarian::where('tanggal_deviden', now()->format('Y-m-d'))->sum('total_member');
        return view('livewire.user.dashboard-user', compact('user', 'devidenHarian',  'totalDevidenBulanLalu'));
    }
}

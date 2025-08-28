<?php

namespace App\Livewire\User;

use App\Models\Aktivitas;
use App\Models\DevidenHarian;
use App\Models\Pembelian;
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
        $isCompletedRObulanan = $user->jml_ro_bulanan >= $user->minimal_ro_bulanan;
        $isStockis = $user->isStockis;
        $totalPembelianStockis = 0;
        if ($isStockis) {
            $totalPembelianStockis = Pembelian::where('beli_dari', auth()->id())->where('status_pembelian', 'menunggu')->count();
        }
        // Fetch aktivitas data for current user
        $aktivitas = Aktivitas::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('livewire.user.dashboard-user', compact('user', 'isStockis', 'devidenHarian', 'totalDevidenBulanLalu', 'isCompletedRObulanan', 'aktivitas', 'totalPembelianStockis'));
    }
}

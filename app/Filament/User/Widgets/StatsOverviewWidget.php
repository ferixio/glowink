<?php

namespace App\Filament\User\Widgets;

use App\Models\Pembelian;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Jumlah Pembelian', Pembelian::where('user_id', auth()->user()->id)->count())
                ->color('success')
                ->icon('heroicon-o-shopping-cart'),
            Stat::make('Jumlah Produk', \App\Models\User::find(auth()->id())?->produkStoks()->sum('stok') ?? 0)
                ->color('success')
                ->icon('heroicon-o-shopping-cart'),
            Stat::make('Karir Level', auth()->user()->plan_karir_sekarang)
                ->color('success')
                ->icon('heroicon-o-shopping-cart'),
            Stat::make('Saldo Penghasilan', 'Rp ' . number_format(auth()->user()->saldo_penghasilan ?? 0, 0, ',', '.'))
                ->color('success')
                ->icon('heroicon-o-shopping-cart'),
        ];
    }
}

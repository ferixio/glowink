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
            // Stat::make('Jumlah Produk', \App\Models\User::find(auth()->id())?->produkStoks()->count() ?? 0)
            //     ->color('success')
            //     ->icon('heroicon-o-shopping-cart'),
            Stat::make('Jumlah Produk', \App\Models\User::find(auth()->id())?->produkStoks()->sum('stok') ?? 0)
                ->color('success')
                ->icon('heroicon-o-shopping-cart'),
        ];
    }
}

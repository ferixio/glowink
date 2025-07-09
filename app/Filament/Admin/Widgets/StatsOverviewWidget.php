<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Jumlah Mitra', User::count())
                ->color('primary')
                ->icon('heroicon-o-users'),
            Stat::make('Mitra Bronze', User::where('plan_karir_sekarang','bronze')->count())
                ->color('primary')
                ->icon('heroicon-o-users'),
            Stat::make('Mitra Silver', User::where('plan_karir_sekarang','silver')->count())
                ->color('primary')
                ->icon('heroicon-o-users'),
        ];
    }
}

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
                ->icon('heroicon-o-users')
                ->url('/admin/user-mitras'),
            Stat::make('Mitra Bronze', User::where('plan_karir_sekarang', 'bronze')->count())
                ->color('primary')
                ->url('/admin/user-mitras?tableFilters[plan_karir_sekarang][value]=bronze')

                ->icon('heroicon-o-users'),
            Stat::make('Mitra Silver', User::where('plan_karir_sekarang', 'silver')->count())
                ->color('primary')
                ->url('/admin/user-mitras?tableFilters[plan_karir_sekarang][value]=silver')
                ->icon('heroicon-o-users'),
            Stat::make('Mitra Gold', User::where('plan_karir_sekarang', 'gold')->count())
                ->color('primary')
                ->url('/admin/user-mitras?tableFilters[plan_karir_sekarang][value]=gold')
                ->icon('heroicon-o-users'),
            Stat::make('Mitra Platinum', User::where('plan_karir_sekarang', 'platinum')->count())
                ->color('primary')
                ->url('/admin/user-mitras?tableFilters[plan_karir_sekarang][value]=platinum')
                ->icon('heroicon-o-users'),
            Stat::make('Mitra Titanium', User::where('plan_karir_sekarang', 'titanium')->count())
                ->color('primary')
                ->url('/admin/user-mitras?tableFilters[plan_karir_sekarang][value]=titanium')
                ->icon('heroicon-o-users'),
            Stat::make('Mitra Ambassador', User::where('plan_karir_sekarang', 'ambassador')->count())
                ->color('primary')
                ->url('/admin/user-mitras?tableFilters[plan_karir_sekarang][value]=ambassador')
                ->icon('heroicon-o-users'),
            Stat::make('Mitra Chairman', User::where('plan_karir_sekarang', 'chairman')->count())
                ->color('primary')
                ->url('/admin/user-mitras?tableFilters[plan_karir_sekarang][value]=chairman')
                ->icon('heroicon-o-users'),

        ];
    }
}

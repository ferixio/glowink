<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $routePath = 'dashboard';

    public static function getNavigationLabel(): string
    {
     return auth()->user()?->isStockis ? 'Dashboard Stokis' : 'Dashboard Mitra';

    }

       public function getTitle(): string | Htmlable
    {
return auth()->user()?->isStockis ? 'Dashboard Stokis' : 'Dashboard Mitra';
    }

    protected static ?string $title = 'Dashboard User';

    

    protected static ?int $navigationSort = -2;

    protected function getHeaderWidgets(): array
    {
        return [
            // \App\Filament\User\Widgets\StatsOverviewWidget::class,
        ];
    }
}

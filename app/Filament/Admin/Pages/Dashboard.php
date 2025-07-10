<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $routePath = 'dashboard';

    public static function getNavigationLabel(): string
    {
        return 'Dashboard Admin';
    }

    protected static ?string $title = 'Dashboard Admin';

    protected static ?int $navigationSort = -2;

    protected function getHeaderWidgets(): array
    {
        return [
            // \App\Filament\Admin\Widgets\StatsOverviewWidget::class,
        ];
    }
}

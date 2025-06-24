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

    protected static ?string $title = 'Dashboard sssssAdmin';

    protected static ?int $navigationSort = -2;
}

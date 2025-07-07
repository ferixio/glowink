<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $routePath = 'dashboard';

    public static function getNavigationLabel(): string
    {
        return 'Dashboard User';
    }

    protected static ?string $title = 'Dashboard User';

    protected static ?int $navigationSort = -2;
}

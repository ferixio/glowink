<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;

class Withdraw extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static string $view = 'filament.user.pages.withdraw';

    protected static ?string $title = 'Withdraw Penghasilan';

    protected static ?string $navigationLabel = 'Withdraw';

    protected static ?string $slug = 'withdraw';

     protected static ?int $navigationSort = 4;
}

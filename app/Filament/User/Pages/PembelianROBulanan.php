<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class PembelianROBulanan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string $view = 'filament.user.pages.pembelian-r-o-bulanan';
    public static function getNavigationLabel(): string
    {
        return 'Belanja RO Bulanan';
    }
    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->status_qr == true;
    }

}

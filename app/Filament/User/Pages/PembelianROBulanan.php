<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;

class PembelianROBulanan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string $view = 'filament.user.pages.pembelian-r-o-bulanan';
       public static function getNavigationLabel(): string
    {
        return 'Belanja RO Bulanan';
    }
    protected static ?int $navigationSort = 2;
}

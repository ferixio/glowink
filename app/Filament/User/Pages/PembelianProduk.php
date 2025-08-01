<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;

class PembelianProduk extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static string $view = 'filament.user.pages.pembelian-produk';

    public static function getNavigationLabel(): string
    {
        return 'Belanja';
    }
    protected static ?string $navigationLabel = null;
    protected static ?int $navigationSort = 2;

}

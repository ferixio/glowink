<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;

class PembelianProdukStokis extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.user.pages.pembelian-produk-stokis';
    protected static ?string $navigationLabel = 'Belanja Stok ke Admin';

    protected static ?string $navigationGroup = "Menu Stockis";

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()?->isStockis == true;
    }

}

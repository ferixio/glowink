<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class PembelianProduk extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static string $view = 'filament.user.pages.pembelian-produk';

    protected static ?string $navigationLabel = null;

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return auth()->user()?->isStockis ? 'Pembelian Produk Stockis' : 'Pembelian Produk Mitra';
    }
    public function getTitle(): string
    {
        $isStockis = Auth::user()?->isStockis ?? false;
        return $isStockis ? 'Pembelian Produk Stockis' : 'Pembelian Produk Mitra';
    }

    protected function getViewData(): array
    {
        return [
            'isStockis' => Auth::user()?->isStockis ?? false,
        ];
    }
}

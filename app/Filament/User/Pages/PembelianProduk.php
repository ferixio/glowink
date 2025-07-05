<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class PembelianProduk extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.user.pages.pembelian-produk';
    public function getTitle(): string
    {
        return '';
    }

    protected function getViewData(): array
    {
        return [
            'isStockis' => Auth::user()?->isStockis ?? false,
        ];
    }
}

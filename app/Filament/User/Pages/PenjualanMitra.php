<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;

class PenjualanMitra extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 7;
    protected static ?string $navigationLabel = 'Jual Stock';

    protected static bool $isDiscovered = false;
    
    protected static string $view = 'filament.user.pages.penjualan-mitra';
}

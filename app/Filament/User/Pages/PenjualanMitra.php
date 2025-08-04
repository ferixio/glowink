<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;

class PenjualanMitra extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationLabel = 'Jual Stock';
    
    protected static string $view = 'filament.user.pages.penjualan-mitra';
}

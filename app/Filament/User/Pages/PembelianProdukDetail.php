<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;
use Livewire\WithFileUploads;

class PembelianProdukDetail extends Page
{
    use WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static bool $shouldRegisterNavigation = false;

}

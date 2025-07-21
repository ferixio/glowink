<?php

namespace App\Filament\User\Pages;

use App\Models\Pembelian;
use App\Models\Setting;
use Filament\Pages\Page;
use Livewire\WithFileUploads;

class PembelianProdukDetail extends Page
{
    use WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static bool $shouldRegisterNavigation = false;

  

}

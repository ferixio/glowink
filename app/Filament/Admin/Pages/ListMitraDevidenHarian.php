<?php

namespace App\Filament\Admin\Pages;

use App\Models\User;
use Filament\Pages\Page;

class ListMitraDevidenHarian extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.list-mitra-deviden-harian';
    protected static bool $shouldRegisterNavigation = false;

    public $mitraList = [];



    public function mount(): void
    {
        // Ambil tanggal dari parameter, default hari ini
        $selectedDate = request()->get('selectedDate', now()->format('Y-m-d'));
        // Query user dengan poin_reward >= 20
        $this->mitraList = User::where('poin_reward', '>=', 20)->get();
    }
}

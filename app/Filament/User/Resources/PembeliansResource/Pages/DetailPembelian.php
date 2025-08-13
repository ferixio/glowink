<?php

namespace App\Filament\User\Resources\PembeliansResource\Pages;

use App\Filament\User\Resources\PembeliansResource;
use App\Models\Pembelian;
use App\Models\Setting;
use Filament\Resources\Pages\ViewRecord;

class DetailPembelian extends ViewRecord
{
    protected static string $resource = PembeliansResource::class;

    protected static string $view = 'filament.user.pages.pembelian-produk-detail';

    public $company = null;
    public function mount($record): void
    {
        $this->record = Pembelian::with(['details.produk', 'seller'])->findOrFail($record);
        if (auth()->user()?->isStockis) {
            $this->company = Setting::first();
        }

    }

    protected function getViewData(): array
    {
        // Fetch pembelian bonuses based on pembelian_id
        $pembelianBonuses = \App\Models\PembelianBonus::where('pembelian_id', $this->record->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'pembelian' => $this->record,
            'stockis' => $this->record->seller,
            'isApprovePage' => false,
            'company' => $this->company,
            'userBaru' => $this->record->user, // User yang dibuat dari aktivasi member
            'pembelianBonuses' => $pembelianBonuses, // Add pembelian bonuses data
        ];
    }

    public static function getSlug(): string
    {
        return '{record}/detail';
    }
}

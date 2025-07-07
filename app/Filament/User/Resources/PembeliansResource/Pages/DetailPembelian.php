<?php

namespace App\Filament\User\Resources\PembeliansResource\Pages;

use App\Filament\User\Resources\PembeliansResource;
use App\Models\Pembelian;
use Filament\Resources\Pages\ViewRecord;

class DetailPembelian extends ViewRecord
{
    protected static string $resource = PembeliansResource::class;

    protected static string $view = 'filament.user.pages.pembelian-produk-detail';

    public function mount($record): void
    {
        $this->record = Pembelian::with(['details.produk', 'seller'])->findOrFail($record);
    }

    protected function getViewData(): array
    {
        return [
            'pembelian' => $this->record,
            'stockis' => $this->record->seller,
            'isApprovePage' => false,
        ];
    }

    public static function getSlug(): string
    {
        return '{record}/detail';
    }
}

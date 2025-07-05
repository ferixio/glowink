<?php

namespace App\Filament\User\Pages;

use App\Models\Pembelian;
use Filament\Pages\Page;

class PembelianProdukDetail extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.user.pages.pembelian-produk-detail';

    public ?Pembelian $pembelian = null;

    public $stockis = null;

    public function mount(int $id): void
    {
        $this->pembelian = Pembelian::with(['details.produk', 'seller'])->findOrFail($id);
   
        $this->stockis = $this->pembelian->seller;
    }

    protected function getViewData(): array
    {
        return [
            'pembelian' => $this->pembelian,
            'stockis' => $this->stockis,
        ];
    }

    public function getTitle(): string
    {
        return 'Detail Pembelian';
    }

    public static function getSlug(): string
    {
        return 'pembelian-detail/{id}';
    }

}

<?php

namespace App\Filament\User\Pages;

use App\Models\Pembelian;
use Filament\Pages\Page;
use Livewire\WithFileUploads;

class PembelianProdukDetail extends Page
{
    use WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.user.pages.pembelian-produk-detail';

    public ?Pembelian $pembelian = null;

    public $stockis = null;

 

    public $bukti_transfer = [];
    protected $rules = [
        'bukti_transfer.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:20480',
    ];

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

    public function uploadBuktiTransfer()
    {
        $this->validate();
        $images = $this->pembelian->images ?? [];
        foreach ($this->bukti_transfer as $file) {
            $path = $file->store('bukti_transfer', 'public');
            $images[] = $path;
        }
        $this->pembelian->images = $images;
        $this->pembelian->save();
        $this->bukti_transfer = [];
        session()->flash('success', 'Bukti transfer berhasil diupload.');
    }

}

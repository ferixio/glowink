<?php

namespace App\Filament\Admin\Pages;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use Filament\Pages\Page;

class ListDetailRepeatOrder extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.admin.pages.list-detail-repeat-order';
    public $pembelianDetails = [];
    public $totalHargaBeli = 0;

    public function mount(): void
    {
        $selectedDate = request()->get('selectedDate', now()->format('Y-m-d'));
        $pembelianIds = Pembelian::where('kategori_pembelian', 'repeat order')
            ->whereDate('tgl_beli', $selectedDate)
            ->whereIn('status_pembelian', ['selesai'])
            ->pluck('id');
        $this->pembelianDetails = PembelianDetail::whereIn('pembelian_id', $pembelianIds)
            ->with(['pembelian.user'])
            ->get();

        // Calculate total harga beli
        $this->totalHargaBeli = $this->pembelianDetails->sum('harga_beli');
    }
}

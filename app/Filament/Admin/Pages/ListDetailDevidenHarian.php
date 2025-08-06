<?php

namespace App\Filament\Admin\Pages;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use Filament\Pages\Page;

class ListDetailDevidenHarian extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.list-detail-deviden-harian';
    protected static bool $shouldRegisterNavigation = false;
    public $pembelianDetails = [];

    public function mount(): void
    {
        $selectedDate = request()->get('selectedDate', now()->format('Y-m-d'));
        $pembelianIds = Pembelian::where('kategori_pembelian', 'aktivasi member')
            ->whereDate('tgl_beli', $selectedDate)
            ->whereIn('status_pembelian', ['selesai'])
            ->pluck('id');
        $this->pembelianDetails = PembelianDetail::where('paket', 1)
            ->whereIn('pembelian_id', $pembelianIds)
            ->with(['pembelian.user'])
            ->get();
    }
}

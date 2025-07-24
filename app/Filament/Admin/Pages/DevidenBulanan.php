<?php

namespace App\Filament\Admin\Pages;

use App\Models\Pembelian;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Log;

class DevidenBulanan extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.deviden-bulanan';
    public array $data = [];
    public function mount()
    {

    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([

                        DatePicker::make('startDate')
                            ->label('Pilih Tanggal Awal')
                            ->default(now()->startOfMonth())
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                $endDate = $get('endDate');
                                if ($state && $endDate) {
                                    $start = \Carbon\Carbon::parse($state);
                                    $end = \Carbon\Carbon::parse($endDate);
                                    if ($start->gt($end)) {
                                        // Hanya set ke tanggal 1 jika user memilih tanggal setelah endDate
                                        $set('startDate', $end->startOfMonth()->toDateString());
                                    }
                                }
                            }),
                        DatePicker::make('endDate')
                            ->label('Pilih Tanggal')
                            ->default(now())
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {

                            }),
                        Actions::make([
                            Action::make('Cari')
                                ->action('pencarianData')
                                ->icon('heroicon-m-magnifying-glass')->extraAttributes(['style' => 'margin-top:2rem']),

                            Action::make('process')
                                ->action('prosesDeviden')
                            // ->label('Proses Deviden Tanggal : ' . $this->data['selectedDate'])
                                ->icon('heroicon-m-arrow-path')->extraAttributes(['style' => 'margin-top:2rem']),

                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function pencarianData()
    {
        $startDate = $this->data['startDate'];
        $endDate = $this->data['endDate'];
        $getPembelians = Pembelian::where('kategori_pembelian', 'repeat order bulanan')->whereBetween('created_at', [$startDate, $endDate])->whereIn('status_pembelian', ['proses', 'selesai'])->get();

        // --- Tambahan logic pencarian jumlah mitra dan transaksi per level karir ---
        $levels = \App\Models\LevelKarir::all();
        $result = [];
        foreach ($levels as $level) {
            $namaLevel = $level->nama_level;
            $minimalROQR = $level->minimal_RO_QR;
            $jumlahMitra = \App\Models\User::where('plan_karir_sekarang', $namaLevel)->count();
            $jumlahMitraTransaksi = \App\Models\User::where('plan_karir_sekarang', $namaLevel)
                ->where('jml_ro_bulanan', '>=', $minimalROQR)
                ->count();
            $result[$namaLevel] = [
                'jumlahMitra' => $jumlahMitra,
                'jumlahMitraTransaksi' => $jumlahMitraTransaksi,
            ];
        }
        $this->data['levelKarirStats'] = $result;

        Log::info('LevelKarirStats:', $result);

        $omsetRObulanan = $getPembelians
            ->flatMap(function ($pembelian) {
                return $pembelian->details->where('paket', 1);
            })
            ->sum('harga_beli');

    }

}

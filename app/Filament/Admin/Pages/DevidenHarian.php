<?php

namespace App\Filament\Admin\Pages;

use App\Models\DevidenHarian as ModelDevidenHarian;
use App\Models\PembelianDetail;
use App\Models\Setting;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class DevidenHarian extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.deviden-harian';

    public ?ModelDevidenHarian $devidenHarian = null;
    public array $data = [];

    public function mount()
    {
        $this->data['selectedDate'] = now()->format('Y-m-d');
        $this->form->fill($this->data);
        $this->loadDevidenHarian();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([

                        DatePicker::make('selectedDate')
                            ->label('Pilih Tanggal')
                            ->default(now())
                            ->reactive()
                            ->afterStateUpdated(fn($state) => $this->updatedSelectedDate($state)),
                        Actions::make([
                            // Action::make('star')
                            //     ->icon('heroicon-m-star')->extraAttributes(['style' => 'margin-top:2rem']),

                            Action::make('process')
                                ->action('prosesDeviden')
                                ->label('Proses Deviden Tanggal : ' . $this->data['selectedDate'])
                                ->icon('heroicon-m-arrow-path')->extraAttributes(['style' => 'margin-top:2rem']),

                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function prosesDeviden()
    {
        $angkaDeviden = Setting::first()->angka_deviden ?? 1500;
        $selectedDate = $this->data['selectedDate'] ?? now()->format('Y-m-d');

        $exists = ModelDevidenHarian::whereDate('tanggal_deviden', $selectedDate)->exists();
        if ($exists) {
            \Filament\Notifications\Notification::make()
                ->title('Gagal')
                ->body('Data deviden harian untuk tanggal ini sudah ada.')
                ->danger()
                ->send();
            return;
        }

        $pembelianIds = \App\Models\Pembelian::where('kategori_pembelian', 'aktivasi member')
            ->whereDate('tgl_beli', $selectedDate)
            ->whereIn('status_pembelian', ['proses', 'selesai'])
            ->pluck('id');
        $detailQuery = PembelianDetail::where('paket', 1)
            ->whereIn('pembelian_id', $pembelianIds);
        $detailIds = $detailQuery->pluck('id');
        $omsetNasionalTotal = PembelianDetail::whereIn('id', $detailIds)->sum('harga_beli');
        $RObasicTotal = \App\Models\Pembelian::whereDate('tgl_beli', $selectedDate)
            ->where('kategori_pembelian', 'repeat order')
            ->whereIn('status_pembelian', ['proses', 'selesai'])
            ->whereHas('details', function ($q) {
                $q->where('paket', 1);
            })
            ->with(['details' => function ($q) {
                $q->where('paket', 1);
            }])
            ->get()
            ->pluck('details')
            ->flatten()
            ->sum('harga_beli');

        $totalMemberUpTo20Point = \App\Models\User::where('poin_reward', '>=', 20)->count();

        if ($totalMemberUpTo20Point > 0) {
            $rumusDeviden = $angkaDeviden * ($omsetNasionalTotal + $RObasicTotal) / $totalMemberUpTo20Point;

            $deviden = ModelDevidenHarian::create([
                'omzet_aktivasi' => $omsetNasionalTotal,
                'omzet_ro_basic' => $RObasicTotal,
                'total_member' => $totalMemberUpTo20Point,
                'deviden_diterima' => $rumusDeviden,
                'tanggal_deviden' => $selectedDate,
            ]);

            \App\Models\User::where('poin_reward', '>=', 20)->get()->each(function ($user) use ($rumusDeviden) {
                $user->saldo_penghasilan += $rumusDeviden;
                $user->save();
            });
            $this->loadDevidenHarian();
            Notification::make()
                ->title('Sukses')
                ->body('Data deviden harian berhasil disimpan.')
                ->success()
                ->send();
        }
    }

    public function updatedSelectedDate($value)
    {

        $this->data['selectedDate'] = $value;
        $this->devidenHarian = null;
        $this->loadDevidenHarian();
    }

    public function searchByDate()
    {
        $this->loadDevidenHarian();
    }

    public function loadDevidenHarian()
    {
        $date = $this->data['selectedDate'] ?? now()->format('Y-m-d');
        $this->devidenHarian = ModelDevidenHarian::whereDate('tanggal_deviden', $date)->latest()->first();
    }
}

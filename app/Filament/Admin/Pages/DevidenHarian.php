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
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static string $view = 'filament.admin.pages.deviden-harian';

    public ?ModelDevidenHarian $devidenHarian = null;
    public array $data = [];
    public $searchResults = null;

    protected static ?int $navigationSort = 1;

    // Monthly statuses for the current month
    public array $monthlyStatuses = [];
    public string $monthLabel = '';
    public string $monthRangeLabel = '';

    public function mount()
    {
        $this->data['selectedDate'] = now()->format('Y-m-d');
        $this->form->fill($this->data);

        $this->generateMonthlyStatuses();
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
                            Action::make('search')
                                ->action('pencarianData')
                                ->label('Cari Data')
                                ->icon('heroicon-m-magnifying-glass')
                                ->extraAttributes(['style' => 'margin-top:2rem']),

                            Action::make('process')
                                ->requiresConfirmation()
                                ->action('prosesDeviden')
                                ->label('Proses Deviden Tanggal : ' . $this->data['selectedDate'])
                                ->icon('heroicon-m-arrow-path')
                                ->extraAttributes(['style' => 'margin-top:2rem']),

                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function pencarianData()
    {
        $selectedDate = $this->data['selectedDate'] ?? now()->format('Y-m-d');

        // Check if data already exists in database
        $existingData = ModelDevidenHarian::whereDate('tanggal_deviden', $selectedDate)->first();

        if ($existingData) {
            $this->devidenHarian = $existingData;
            $this->searchResults = null;
            Notification::make()
                ->title('Data Ditemukan')
                ->body('Data deviden harian untuk tanggal ini sudah ada di database.')
                ->success()
                ->send();
            return;
        }

        // Calculate data without saving to database
        $angkaDeviden = Setting::first()->angka_deviden ?? 1500;

        $pembelianIds = \App\Models\Pembelian::where('kategori_pembelian', 'aktivasi member')
            ->whereDate('tgl_beli', $selectedDate)
            ->whereIn('status_pembelian', ['selesai'])
            ->pluck('id');
        $detailQuery = PembelianDetail::where('paket', 1)
            ->whereIn('pembelian_id', $pembelianIds);
        $detailIds = $detailQuery->pluck('id');
        $omsetNasionalTotal = PembelianDetail::whereIn('id', $detailIds)->count();
        // dd($omsetNasionalTotal);
        // Query RO Basic yang sudah diperbaiki dan dioptimalkan
        $RObasicTotal = PembelianDetail::where('paket', 1)
            ->whereHas('pembelian', function ($q) use ($selectedDate) {
                $q->whereDate('tgl_beli', $selectedDate)
                    ->where('kategori_pembelian', 'repeat order')
                    ->whereIn('status_pembelian', ['selesai']);
            })
            ->count();

        $totalMemberUpTo20Point = \App\Models\User::where('poin_reward', '>=', 20)->count();

        if ($totalMemberUpTo20Point > 0) {
            $rumusDeviden = $angkaDeviden * ($omsetNasionalTotal + $RObasicTotal) / $totalMemberUpTo20Point;

            // Create temporary object for display
            $this->searchResults = (object) [
                'omzet_aktivasi' => $omsetNasionalTotal,
                'omzet_ro_basic' => $RObasicTotal,
                'total_member' => $totalMemberUpTo20Point,
                'deviden_diterima' => $rumusDeviden,
                'created_at' => now(),
            ];

            $this->devidenHarian = null;

            Notification::make()
                ->title('Data Ditemukan')
                ->body('Data deviden harian untuk tanggal ini berhasil dihitung.')
                ->success()
                ->send();
        } else {
            $this->searchResults = null;
            $this->devidenHarian = null;

            Notification::make()
                ->title('Data Tidak Ditemukan')
                ->body('Tidak ada data yang dapat dihitung untuk tanggal ini.')
                ->warning()
                ->send();
        }
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
            ->whereIn('status_pembelian', ['selesai'])
            ->pluck('id');
        $detailQuery = PembelianDetail::where('paket', 1)
            ->whereIn('pembelian_id', $pembelianIds);
        $detailIds = $detailQuery->pluck('id');
        $omsetNasionalTotal = PembelianDetail::whereIn('id', $detailIds)->count();
        // Query RO Basic yang sudah diperbaiki dan dioptimalkan
        $RObasicTotal = PembelianDetail::where('paket', 1)
            ->whereHas('pembelian', function ($q) use ($selectedDate) {
                $q->whereDate('tgl_beli', $selectedDate)
                    ->where('kategori_pembelian', 'repeat order')
                    ->whereIn('status_pembelian', ['selesai']);
            })
            ->count();

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

            $this->makeIncomeForUser();
            $this->loadDevidenHarian();
            $this->searchResults = null;
            Notification::make()
                ->title('Sukses')
                ->body('Data deviden harian berhasil disimpan.')
                ->success()
                ->send();

            // refresh monthly statuses after processing
            $this->generateMonthlyStatuses();
        }
    }

    public function updatedSelectedDate($value)
    {
        $this->data['selectedDate'] = $value;
        $this->devidenHarian = null;
        $this->searchResults = null;
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

    public function makeIncomeForUser()
    {
        $devidenHarian = \App\Models\DevidenHarian::where('tanggal_deviden', $this->data['selectedDate'])->first();
        $users = \App\Models\User::where('poin_reward', '>=', 20)->get();

        foreach ($users as $user) {
            // Update user's saldo_penghasilan
            $user->saldo_penghasilan += $devidenHarian->deviden_diterima;
            $user->save();

            // Create record in Penghasilan table
            \App\Models\Penghasilan::create([
                'user_id' => $user->id,
                'tgl_dapat_bonus' => $this->data['selectedDate'],
                'keterangan' => 'Deviden Harian - ' . $this->data['selectedDate'],
                'nominal_bonus' => $devidenHarian->deviden_diterima,
                'kategori_bonus' => 'deviden harian',
                'status_qr' => 'selesai',
            ]);

            \App\Models\Aktivitas::create([
                'user_id' => $user->id,
                'judul' => 'Deviden Harian',
                'keterangan' => 'Menerima deviden harian ',
                'status' => 'success',
                'tipe' => 'plus',
                'nominal' => $devidenHarian->deviden_diterima,
            ]);

        }

        Notification::make()
            ->title('Data berhasil disimpan')
            ->body('Data deviden harian dan detail telah berhasil disimpan ke database.')
            ->success()
            ->send();
    }

    private function generateMonthlyStatuses(): void
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $this->monthLabel = $startOfMonth->translatedFormat('F Y');
        $this->monthRangeLabel = $startOfMonth->translatedFormat('d M Y') . ' - ' . $endOfMonth->translatedFormat('d M Y');

        $statuses = [];
        $date = $startOfMonth->copy();
        while ($date->lte($endOfMonth)) {
            $dateString = $date->format('Y-m-d');
            $exists = ModelDevidenHarian::whereDate('tanggal_deviden', $dateString)->exists();
            $statuses[] = [
                'date' => $dateString,
                'processed' => $exists,
                'omzet_aktivasi' => $exists ? ModelDevidenHarian::whereDate('tanggal_deviden', $dateString)->first()->omzet_aktivasi : null,
                'omzet_ro_basic' => $exists ? ModelDevidenHarian::whereDate('tanggal_deviden', $dateString)->first()->omzet_ro_basic : null,
                'total_member' => $exists ? ModelDevidenHarian::whereDate('tanggal_deviden', $dateString)->first()->total_member : null,
            ];
            $date->addDay();
        }

        $this->monthlyStatuses = $statuses;
    }
}

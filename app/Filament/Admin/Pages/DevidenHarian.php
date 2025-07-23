<?php

namespace App\Filament\Admin\Pages;

use App\Models\DevidenHarian as ModelDevidenHarian;
use App\Models\PembelianDetail;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Log;

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
                            Action::make('star')
                                ->icon('heroicon-m-star')->extraAttributes(['style' => 'margin-top:2rem']),
                            Action::make('process')
                                ->action('prosesDeviden')
                                ->label('Proses bagi deviden')
                                ->icon('heroicon-m-arrow-path')->extraAttributes(['style' => 'margin-top:2rem']),

                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function prosesDeviden()
    {
        $angkaDeviden = 1500;
        Log::info('angkaDeviden: ' . $angkaDeviden);
        // Step 1: Ambil id pembelian yang kategori_pembelian = 'aktivasi member' dan tgl_beli hari ini
        $pembelianIds = \App\Models\Pembelian::where('kategori_pembelian', 'aktivasi member')
            ->whereDate('tgl_beli', now()->format('Y-m-d'))
            ->pluck('id');
        Log::info('Step1_pembelianIds: ' . json_encode($pembelianIds));

        // Step 2: Ambil detail dengan paket = 1 dan pembelian_id hasil step 1
        $detailQuery = PembelianDetail::where('paket', 1)
            ->whereIn('pembelian_id', $pembelianIds);
        $detailIds = $detailQuery->pluck('id');
        Log::info('Step2_detailIds: ' . json_encode($detailIds));

        // Step 3: Sum harga_beli dari detail tersebut
        $omsetNasionalTotal = PembelianDetail::whereIn('id', $detailIds)->sum('harga_beli');
        Log::info('Step3_omsetNasionalTotal: ' . $omsetNasionalTotal);

        Log::info('omsetNasionalTotal: ' . $omsetNasionalTotal);

        $RObasicTotal = \App\Models\Pembelian::whereDate('tgl_beli', now()->format('Y-m-d'))
            ->where('kategori_pembelian', 'repeat order')
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

        Log::info('RObasicTotal: ' . $RObasicTotal);

        $totalMemberUpTo20Point = \App\Models\User::where('poin_reward', '>=', 20)->count();
        Log::info('totalMemberUpTo20Point: ' . $totalMemberUpTo20Point);

        if ($totalMemberUpTo20Point > 0) {
            $rumusDeviden = $angkaDeviden * ($omsetNasionalTotal + $RObasicTotal) / $totalMemberUpTo20Point;
            Log::info('rumusDeviden: ' . $rumusDeviden);

            // Simpan ke tabel deviden_harians
            $deviden = ModelDevidenHarian::create([
                'omzet_aktivasi' => $omsetNasionalTotal,
                'omzet_ro_basic' => $RObasicTotal,
                'total_member' => $totalMemberUpTo20Point,
                'deviden_diterima' => $rumusDeviden,
            ]);
            $this->loadDevidenHarian();
            Log::info('DevidenHarian created: ' . json_encode($deviden));
            Notification::make()
                ->title('Sukses')
                ->body('Data deviden harian berhasil disimpan.')
                ->success()
                ->send();
        } else {
            Log::warning('Tidak ada member dengan poin_reward >= 20, pembagian deviden tidak dapat dilakukan.');
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
        $this->devidenHarian = ModelDevidenHarian::whereDate('created_at', $date)->latest()->first();
    }
}

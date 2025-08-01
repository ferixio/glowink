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
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DevidenBulanan extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.deviden-bulanan';
    public array $data = [];

    // Properties to store search results
    public $devidenBulananData = null;
    public $detailDevidenBulananData = [];
    public $searchPerformed = false;
    public $omzetROQR = 0;

    protected $listeners = ['makeIncomeForUser' => 'makeIncomeForUser'];

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
                                ->action('processDevidenBulanan')
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

        Log::info('getPembelians:', $getPembelians->toArray());
        $omsetRObulanan = $getPembelians
            ->flatMap(function ($pembelian) {
                return $pembelian->details->where('paket', 2);
            })
            ->sum('harga_beli');
        $this->omzetROQR = $omsetRObulanan;
        Log::info('omsetRObulanan:', ['value' => $omsetRObulanan]);

        // Simpan data ke tabel deviden_bulanans (dalam bentuk array untuk ditampilkan)
        $this->devidenBulananData = [
            'tanggal_input' => now()->toDateString(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'omzet_ro_qr' => $omsetRObulanan,
        ];

        // Simpan detail untuk setiap level karir (dalam bentuk array untuk ditampilkan)
        $this->detailDevidenBulananData = [];
        foreach ($result as $namaLevel => $stats) {
            $levelKarir = \App\Models\LevelKarir::where('nama_level', $namaLevel)->first();
            if ($levelKarir) {
                $nominalDevidenBulanan = 0;
                if ($stats['jumlahMitra'] > 0) {
                    $nominalDevidenBulanan = ($levelKarir->angka_deviden * $omsetRObulanan) / $stats['jumlahMitra'];
                }

                $this->detailDevidenBulananData[] = [
                    'nama_level' => $namaLevel,
                    'jumlah_mitra' => $stats['jumlahMitra'],
                    'jumlah_mitra_transaksi' => $stats['jumlahMitraTransaksi'],
                    'omzet_ro_qr' => $omsetRObulanan,
                    'angka_deviden' => $levelKarir->angka_deviden,
                    'nominal_deviden_bulanan' => $nominalDevidenBulanan,
                ];

                // Log data yang akan disimpan ke detail_deviden_bulanans
                Log::info("Detail Deviden Bulanan untuk level {$namaLevel}:", [
                    'nama_level' => $namaLevel,
                    'jumlah_mitra' => $stats['jumlahMitra'],
                    'jumlah_mitra_transaksi' => $stats['jumlahMitraTransaksi'],
                    'omzet_ro_qr' => $omsetRObulanan,
                    'angka_deviden' => $levelKarir->angka_deviden,
                    'nominal_deviden_bulanan' => $nominalDevidenBulanan,
                ]);
            }
        }

        $this->searchPerformed = true;
        Log::info('Data berhasil disimpan ke tabel deviden_bulanans dan detail_deviden_bulanans');

    }

    public function processDevidenBulanan()
    {
        // Validasi apakah data pencarian sudah dilakukan
        if (!$this->searchPerformed || !$this->devidenBulananData) {
            Notification::make()
                ->title('Data tidak ditemukan')
                ->body('Silakan lakukan pencarian data terlebih dahulu.')
                ->warning()
                ->send();
            return;
        }

        try {
            // Mulai transaction untuk memastikan data konsisten
            DB::beginTransaction();

            // Simpan data ke tabel deviden_bulanans
            $devidenBulanan = \App\Models\DevidenBulanan::create([
                'tanggal_input' => $this->devidenBulananData['tanggal_input'],
                'start_date' => $this->devidenBulananData['start_date'],
                'end_date' => $this->devidenBulananData['end_date'],
                'total_deviden_bulanan' => $this->devidenBulananData['omzet_ro_qr'],
            ]);

            // Simpan detail untuk setiap level karir
            foreach ($this->detailDevidenBulananData as $detail) {
                \App\Models\DetailDevidenBulanan::create([
                    'deviden_bulanan_id' => $devidenBulanan->id,
                    'nama_level' => $detail['nama_level'],
                    'jumlah_mitra' => $detail['jumlah_mitra'],
                    'jumlah_mitra_transaksi' => $detail['jumlah_mitra_transaksi'],
                    'omzet_ro_qr' => $detail['omzet_ro_qr'],
                    'angka_deviden' => $detail['angka_deviden'],
                    'nominal_deviden_bulanan' => $detail['nominal_deviden_bulanan'],
                ]);
            }

            // Commit transaction
            DB::commit();
            $this->makeIncomeForUser();

            // Notifikasi sukses
            Notification::make()
                ->title('Data berhasil disimpan')
                ->body('Data deviden bulanan dan detail telah berhasil disimpan ke database.')
                ->success()
                ->send();

            // Reset data setelah berhasil disimpan
            $this->searchPerformed = false;
            $this->devidenBulananData = null;
            $this->detailDevidenBulananData = [];

            Log::info('Data deviden bulanan berhasil disimpan:', [
                'deviden_bulanan_id' => $devidenBulanan->id,
                'total_details' => count($this->detailDevidenBulananData),
            ]);

        } catch (\Exception $e) {
            // Rollback transaction jika terjadi error
            DB::rollBack();

            // Notifikasi error
            Notification::make()
                ->title('Gagal menyimpan data pada saat proses')
                ->body('Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                ->danger()
                ->send();

            Log::error('Error saat menyimpan deviden bulanan:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function makeIncomeForUser()
    {
        try {
            // Mulai transaction untuk memastikan data konsisten
            DB::beginTransaction();

            $devidenBulanan = \App\Models\DevidenBulanan::where('tanggal_input', $this->devidenBulananData['tanggal_input'])->first();

            if (!$devidenBulanan) {
                Notification::make()
                    ->title('Data tidak ditemukan')
                    ->body('Data deviden bulanan tidak ditemukan untuk tanggal tersebut.')
                    ->warning()
                    ->send();
                return;
            }

            $detailDevidenBulanan = \App\Models\DetailDevidenBulanan::where('deviden_bulanan_id', $devidenBulanan->id)->get();
            $totalUsersUpdated = 0;
            $totalIncomeDistributed = 0;

            // Ambil semua level karir untuk perbandingan poin_reward
            $allLevelKarir = \App\Models\LevelKarir::orderBy('poin_reward', 'asc')->get();

            foreach ($detailDevidenBulanan as $detail) {
                // Ambil level karir untuk mendapatkan minimal RO QR dan poin_reward
                $levelKarir = \App\Models\LevelKarir::where('nama_level', $detail->nama_level)->first();
                $minimalROQR = $levelKarir ? $levelKarir->minimal_RO_QR : 0;
                $levelPoinReward = $levelKarir ? $levelKarir->poin_reward : 0;

                // Ambil semua user yang memenuhi syarat minimal RO QR
                $users = \App\Models\User::where('plan_karir_sekarang', $detail->nama_level)
                    ->where('jml_ro_bulanan', '>=', $minimalROQR)
                    ->get();

                foreach ($users as $user) {
                    // Hitung total deviden yang akan diterima berdasarkan poin_reward user
                    $totalDevidenForUser = 0;
                    $devidenDetails = [];

                    // Loop melalui semua level karir untuk mengecek poin_reward
                    foreach ($allLevelKarir as $level) {
                        // Jika poin_reward user >= poin_reward level, user berhak mendapat deviden dari level tersebut
                        if ($user->poin_reward >= $level->poin_reward) {
                            // Cari detail deviden untuk level ini
                            $levelDetail = $detailDevidenBulanan->where('nama_level', $level->nama_level)->first();

                            if ($levelDetail) {
                                $totalDevidenForUser += $levelDetail->nominal_deviden_bulanan;
                                $devidenDetails[] = [
                                    'level' => $level->nama_level,
                                    'poin_reward_level' => $level->poin_reward,
                                    'nominal' => $levelDetail->nominal_deviden_bulanan,
                                ];
                            }
                        }
                    }

                    // Jika user berhak mendapat deviden
                    if ($totalDevidenForUser > 0) {
                        // Update saldo penghasilan user dan reset jml_ro_bulanan
                        $oldSaldo = $user->saldo_penghasilan;
                        $oldJmlROBulanan = $user->jml_ro_bulanan;
                        $user->saldo_penghasilan += $totalDevidenForUser;
                        $user->jml_ro_bulanan = 0; // Reset jml_ro_bulanan menjadi 0
                        $user->save();

                        // Catat history ke tabel Penghasilan
                        $keterangan = "Deviden Bulanan - Poin Reward: {$user->poin_reward} - Periode {$this->devidenBulananData['start_date']} s/d {$this->devidenBulananData['end_date']}";
                        if (!empty($devidenDetails)) {
                            $keterangan .= " - Level: " . implode(', ', array_column($devidenDetails, 'level'));
                        }
                        $keterangan .= " - RO Bulanan: {$oldJmlROBulanan} → 0 (Reset)";

                        \App\Models\Penghasilan::create([
                            'user_id' => $user->id,
                            'tgl_dapat_bonus' => $this->devidenBulananData['tanggal_input'],
                            'keterangan' => $keterangan,
                            'nominal_bonus' => $totalDevidenForUser,
                            'kategori_bonus' => 'deviden bulanan',
                            'status_qr' => 'selesai',
                        ]);

                        $totalUsersUpdated++;
                        $totalIncomeDistributed += $totalDevidenForUser;

                        // Log untuk tracking
                        Log::info("User {$user->name} (ID: {$user->id}) mendapat deviden bulanan:", [
                            'user_poin_reward' => $user->poin_reward,
                            'old_saldo' => $oldSaldo,
                            'new_saldo' => $user->saldo_penghasilan,
                            'old_jml_ro_bulanan' => $oldJmlROBulanan,
                            'new_jml_ro_bulanan' => 0,
                            'total_deviden' => $totalDevidenForUser,
                            'deviden_details' => $devidenDetails,
                            'periode' => $this->devidenBulananData['start_date'] . ' s/d ' . $this->devidenBulananData['end_date'],
                        ]);
                    }
                }
            }

            // Commit transaction
            DB::commit();

            // Notifikasi sukses dengan detail
            Notification::make()
                ->title('Penghasilan berhasil didistribusikan')
                ->body("Total {$totalUsersUpdated} user telah menerima deviden bulanan sebesar Rp " . number_format($totalIncomeDistributed, 0, ',', '.') . " untuk periode {$this->devidenBulananData['start_date']} s/d {$this->devidenBulananData['end_date']}")
                ->success()
                ->send();

            Log::info('Distribusi deviden bulanan berhasil:', [
                'tanggal_input' => $this->devidenBulananData['tanggal_input'],
                'total_users_updated' => $totalUsersUpdated,
                'total_income_distributed' => $totalIncomeDistributed,
                'periode' => $this->devidenBulananData['start_date'] . ' s/d ' . $this->devidenBulananData['end_date'],
            ]);

        } catch (\Exception $e) {
            // Rollback transaction jika terjadi error
            DB::rollBack();

            // Notifikasi error
            Notification::make()
                ->title('Gagal mendistribusikan penghasilan')
                ->body('Terjadi kesalahan saat mendistribusikan deviden bulanan: ' . $e->getMessage())
                ->danger()
                ->send();

            Log::error('Error saat mendistribusikan deviden bulanan:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

}

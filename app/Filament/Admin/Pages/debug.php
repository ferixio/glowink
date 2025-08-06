<?php

namespace App\Filament\Admin\Pages;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class debug extends Page
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-wrench';
    protected static ?int $navigationSort = 5;
    protected static string $view = 'filament.admin.pages.debug';

    public ?array $data = [];
    public string $activeTab = 'deviden';

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Pilih User')
                    ->options(User::whereNotNull('nama')->get()->mapWithKeys(function ($user) {
                        return [$user->id => $user->nama . ' (' . ($user->plan_karir_sekarang ?? 'Belum ada plan') . ')'];
                    }))
                    ->searchable()
                    ->required()
                    ->placeholder('Pilih user untuk seeding...'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        // $data = $this->form->getState();

        // Notification::make()
        //     ->title('User berhasil dipilih')
        //     ->success()
        //     ->send();
    }

    public function makePurchaseForROBasic(): void
    {
        $data = $this->form->getState();

        $user = User::find($data['user_id']);
        $pembelian = \App\Models\Pembelian::create([
            'tgl_beli' => now()->toDateString(),
            'user_id' => $user->id,
            'beli_dari' => 2, // ID stockist yang berbeda dari user pembeli
            'tujuan_beli' => 'Stock Pribadi',
            'nama_penerima' => $user->nama,
            'no_telp' => $user->no_telp ?? '08123456789',
            'alamat_tujuan' => $user->alamat ?? 'Alamat Default',
            'total_beli' => 100000,
            'total_bonus' => 100000,
            'status_pembelian' => 'selesai', // Ubah ke 'diterima' agar diselesai
            'kategori_pembelian' => 'repeat order',
            'total_cashback' => 100000,
            'jumlah_poin_qr' => 1,
        ]);

        $pembelianDetail = \App\Models\PembelianDetail::create([
            'pembelian_id' => $pembelian->id,
            'produk_id' => 1,
            'nama_produk' => 'RO Basic',
            'paket' => 1,
            'jml_beli' => 1,
            'harga_beli' => 10000,
            'nominal_bonus_sponsor' => 10000,
            'nominal_bonus_generasi' => 10000,
            'user_id_get_bonus_sponsor' => $user->id,
            'group_user_id_get_bonus_generasi' => $user->id,
            'cashback' => 10000,
        ]);

        // Trigger event untuk memselesai pembelian
        event(new \App\Events\PembelianDiterima($pembelian));

        Notification::make()
            ->title('User ' . $user->nama . ' berhasil melakukan pembelian RO Basic')
            ->success()
            ->send();
    }

    public function makePurchaseForROBulanan(): void
    {
        $data = $this->form->getState();

        $user = User::find($data['user_id']);
        $pembelian = \App\Models\Pembelian::create([
            'tgl_beli' => now()->toDateString(),
            'user_id' => $user->id,
            'beli_dari' => 2, // ID stockist yang berbeda dari user pembeli
            'tujuan_beli' => 'Stock Pribadi',
            'nama_penerima' => $user->nama,
            'no_telp' => $user->no_telp ?? '08123456789',
            'alamat_tujuan' => $user->alamat ?? 'Alamat Default',
            'total_beli' => 100000,
            'total_bonus' => 100000,
            'status_pembelian' => 'selesai', // Ubah ke 'diterima' agar diselesai
            'kategori_pembelian' => 'repeat order bulanan',
            'total_cashback' => 100000,
            'jumlah_poin_qr' => 1,
        ]);

        $pembelianDetail = \App\Models\PembelianDetail::create([
            'pembelian_id' => $pembelian->id,
            'produk_id' => 2,
            'nama_produk' => 'QR. BARILKHA COFFEE (Pouch) 300g',
            'paket' => 2,
            'jml_beli' => 1,
            'harga_beli' => 100000,
            'nominal_bonus_sponsor' => 0,
            'nominal_bonus_generasi' => 0,
            'user_id_get_bonus_sponsor' => $user->id,
            'group_user_id_get_bonus_generasi' => $user->id,
            'cashback' => 0,
        ]);

        // Trigger event untuk memselesai pembelian
        event(new \App\Events\PembelianDiterima($pembelian));

        Notification::make()
            ->title('User ' . $user->nama . ' berhasil melakukan pembelian RO Bulanan')
            ->success()
            ->send();
    }

    // public function makePurchaseForAktivasiMember(): void
    // {
    //     $data = $this->form->getState();

    //     $user = User::find($data['user_id']);
    //     $pembelian = \App\Models\Pembelian::create([
    //         'tgl_beli' => now()->toDateString(),
    //         'user_id' => $user->id,
    //         'beli_dari' => 2, // ID stockist yang berbeda dari user pembeli
    //         'tujuan_beli' => 'Aktivasi Member',
    //         'nama_penerima' => $user->nama,
    //         'no_telp' => $user->no_telp ?? '08123456789',
    //         'alamat_tujuan' => $user->alamat ?? 'Alamat Default',
    //         'total_beli' => 500000,
    //         'total_bonus' => 500000,
    //         'status_pembelian' => 'selesai',
    //         'kategori_pembelian' => 'aktivasi member',
    //         'total_cashback' => 50000,
    //         'jumlah_poin_qr' => 5,
    //     ]);

    //     $pembelianDetail = \App\Models\PembelianDetail::create([
    //         'pembelian_id' => $pembelian->id,
    //         'produk_id' => 1,
    //         'nama_produk' => 'Paket Aktivasi Member',
    //         'paket' => 'Paket Aktivasi',
    //         'jml_beli' => 1,
    //         'harga_beli' => 500000,
    //         'nominal_bonus_sponsor' => 100000,
    //         'nominal_bonus_generasi' => 50000,
    //         'user_id_get_bonus_sponsor' => $user->id_sponsor ?? 1,
    //         'group_user_id_get_bonus_generasi' => $user->group_sponsor ?? '1',
    //         'cashback' => 50000,
    //     ]);

    //     // Trigger event untuk memselesai pembelian
    //     event(new \App\Events\PembelianDiterima($pembelian));

    //     Notification::make()
    //         ->title('User ' . $user->nama . ' berhasil melakukan aktivasi member')
    //         ->success()
    //         ->send();
    // }

    // public function makePurchaseForRepeatOrder(): void
    // {
    //     $data = $this->form->getState();

    //     $user = User::find($data['user_id']);
    //     $pembelian = \App\Models\Pembelian::create([
    //         'tgl_beli' => now()->toDateString(),
    //         'user_id' => $user->id,
    //         'beli_dari' => 2, // ID stockist yang berbeda dari user pembeli
    //         'tujuan_beli' => 'Repeat Order',
    //         'nama_penerima' => $user->nama,
    //         'no_telp' => $user->no_telp ?? '08123456789',
    //         'alamat_tujuan' => $user->alamat ?? 'Alamat Default',
    //         'total_beli' => 100000,
    //         'total_bonus' => 100000,
    //         'status_pembelian' => 'selesai',
    //         'kategori_pembelian' => 'repeat order',
    //         'total_cashback' => 10000,
    //         'jumlah_poin_qr' => 1,
    //     ]);

    //     $pembelianDetail = \App\Models\PembelianDetail::create([
    //         'pembelian_id' => $pembelian->id,
    //         'produk_id' => 1,
    //         'nama_produk' => 'Produk Regular',
    //         'paket' => 'Regular',
    //         'jml_beli' => 1,
    //         'harga_beli' => 100000,
    //         'nominal_bonus_sponsor' => 10000,
    //         'nominal_bonus_generasi' => 5000,
    //         'user_id_get_bonus_sponsor' => $user->id_sponsor ?? 1,
    //         'group_user_id_get_bonus_generasi' => is_array($user->group_sponsor) ? implode(',', $user->group_sponsor) : ($user->group_sponsor ?? '1'),
    //         'cashback' => 10000,
    //     ]);

    //     // Trigger event untuk memselesai pembelian
    //     event(new \App\Events\PembelianDiterima($pembelian));

    //     Notification::make()
    //         ->title('User ' . $user->nama . ' berhasil melakukan repeat order')
    //         ->success()
    //         ->send();
    // }

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Action::make('seed_ro_basic')
    //             ->label('Seed RO Basic')
    //             ->color('success')
    //             ->icon('heroicon-o-plus-circle')
    //             ->action(function () {
    //                 // Logic untuk seed RO basic akan ditambahkan di sini
    //                 Notification::make()
    //                     ->title('RO Basic Seeded')
    //                     ->success()
    //                     ->send();
    //             })
    //             ->requiresConfirmation()
    //             ->modalHeading('Seed RO Basic')
    //             ->modalDescription('Apakah Anda yakin ingin melakukan seed RO Basic?')
    //             ->modalSubmitActionLabel('Ya, Seed RO Basic'),

    //         Action::make('seed_ro_bulanan')
    //             ->label('Seed RO Bulanan')
    //             ->color('warning')
    //             ->icon('heroicon-o-calendar')
    //             ->action(function () {
    //                 // Logic untuk seed RO bulanan akan ditambahkan di sini
    //                 Notification::make()
    //                     ->title('RO Bulanan Seeded')
    //                     ->success()
    //                     ->send();
    //             })
    //             ->requiresConfirmation()
    //             ->modalHeading('Seed RO Bulanan')
    //             ->modalDescription('Apakah Anda yakin ingin melakukan seed RO Bulanan?')
    //             ->modalSubmitActionLabel('Ya, Seed RO Bulanan'),
    //     ];
    // }
}

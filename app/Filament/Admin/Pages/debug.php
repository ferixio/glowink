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
                    ->options(User::whereNotNull('nama')->pluck('nama', 'id'))
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
            'user_id' => $user->id,
            'tgl_pembelian' => now(),
            'jml_ro_basic' => 1,
            'harga_beli' => 100000,
            'status_pembelian' => 'selesai',
            'kategori_pembelian' => 'ro_basic',
            'status_qr' => 'selesai',
        ]);
        $pembelianDetail = \App\Models\PembelianDetail::create([
            'pembelian_id' => $pembelian->id,
            'produk_id' => 1,
            'nama_produk' => 'RO Basic',
            'paket' => 'RO Basic',
            'jml_beli' => 1,
            'harga_beli' => 100000,
            'nominal_bonus_sponsor' => 100000,
            'nominal_bonus_generasi' => 100000,
            'user_id_get_bonus_sponsor' => $user->id,
            'group_user_id_get_bonus_generasi' => $user->id,
            'cashback' => 100000,
        ]);
        $user->jml_ro_basic = 1;
        $user->save();

        Notification::make()
            ->title('User ' . $user->nama . ' berhasil melakukan pembelian RO Basic')
            ->success()
            ->send();
    }

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

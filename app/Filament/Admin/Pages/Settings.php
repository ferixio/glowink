<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as ActionsAction;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.admin.pages.settings';
protected static ?int $navigationSort = 4;
    public ?Setting $companyProfile = null;

    public ?array $data = [];
    protected static ?string $title = 'Settings';
    protected static ?string $navigationLabel = 'Settings';

    public function mount()
    {
        $this->companyProfile = Setting::first() ?? new Setting();

        $this->form->fill($this->companyProfile->toArray());
    }

    protected function getFormActions(): array
    {
        return [

        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([

            Fieldset::make('Data Perusahaan')->schema([
                TextInput::make('nama'),
                TextInput::make('email')->email(),
                TextInput::make('alamat'),

                TextInput::make('telepon')->label('Telepon'),
                TextInput::make('bank_name')->label('Nama Bank'),
                TextInput::make('bank_atas_nama')->label('Bank Atas Nama'),
                TextInput::make('no_rek')->label('Nomer Rekening'),

            ]),

            Actions::make([
                ActionsAction::make('save')->color('success')->label('Simpan Perubahan')->submit('save'),

            ]),

            Fieldset::make('Data Website')->schema([
                Actions::make([
                    ActionsAction::make('resetData')
                        ->color('danger')
                        ->label('Reset Semua Data (Kecuali Admin)')
                        ->action('resetData')
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Reset Data')
                        ->modalDescription('Semua data akan dihapus kecuali user admin. Lanjutkan?'),
                    ActionsAction::make('runSeeders')
                        ->color('primary')
                        ->label('Add Dummy Data')
                        ->action('runSeeders')
                        ->requiresConfirmation()
                        ->modalHeading('Konfirmasi Jalankan Seeder')
                        ->modalDescription('Semua seeder akan dijalankan. Lanjutkan?'),
                ])]),

        ])->statePath('data');
    }

    public function save()
    {
        $this->companyProfile->fill($this->form->getState());
        $this->companyProfile->save();

        Notification::make()
            ->title('Company profile updated successfully!')
            ->success()
            ->send();
    }

    public function resetData()
    {
        // Nonaktifkan foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Hapus data dari tabel anak ke induk (urutan penting!)
        \App\Models\ProdukStok::truncate();
        \App\Models\PembelianDetail::truncate();
        \App\Models\Pembelian::truncate();
        \App\Models\Penghasilan::truncate();
        \App\Models\Aktivitas::truncate();
        \App\Models\JaringanMitra::truncate();
        \App\Models\Withdraw::truncate();
        \App\Models\UserHistoryStatusMember::truncate();
        \App\Models\Setting::truncate();
        \App\Models\Produk::truncate();

        // Hapus user yang bukan admin (jangan truncate users!)
        \App\Models\User::where('isAdmin', false)->delete();

        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Notification::make()
            ->title('Semua data berhasil direset, kecuali user admin!')
            ->success()
            ->send();
    }

    public function runSeeders()
    {
        try {
            Artisan::call('db:seed', ['--force' => true]);
            Notification::make()
                ->title('Semua seeder berhasil dijalankan!')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal menjalankan seeder: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

}

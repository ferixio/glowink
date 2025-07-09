<?php

namespace App\Filament\User\Pages;

use App\Services\LocationService;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as ActionsAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static string $view = 'filament.user.pages.profile';
    // protected static bool $isDiscovered = false;

    protected static ?int $navigationSort = 6;

    protected int|string|array $columnSpan = 'full';

    public ?array $data = [];

    public function mount()
    {
        $user = Auth::user();
        $fields = [
            'nama', 'email', 'no_telp', 'alamat', 'provinsi', 'kabupaten', 'no_rek', 'nama_rekening', 'bank',
        ];
        $data = [];
        foreach ($fields as $field) {
            $data[$field] = $user->{$field} ?? null;
        }
        $this->form->fill($data);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                TextInput::make('nama')->label('Nama Lengkap')->required(),
                TextInput::make('email')->email()->required(),
                TextInput::make('no_telp')->label('No. Telepon')->tel()->required(),
                Select::make('provinsi')
                    ->label('Provinsi')
                    ->options(LocationService::getProvinces())
                    ->required()
                    ->reactive(),
                Select::make('kabupaten')
                    ->label('Kabupaten')
                    ->options(fn($get) => collect(LocationService::getRegenciesByProvince($get('provinsi') ?? ''))->pluck('name', 'id')->toArray())
                    ->required()
                    ->reactive(),
                Textarea::make('alamat')->required()->columnSpanFull(),
                TextInput::make('no_rek')->label('No. Rekening'),
                TextInput::make('nama_rekening')->label('Nama Rekening'),
                TextInput::make('bank')->label('Bank'),
            ]),
            Actions::make([
                ActionsAction::make('update')
                    ->color('success')
                    ->label('Update')
                    ->submit('update'),
            ]),
        ])->statePath('data');
    }

    public function update()
    {
        $user = \App\Models\User::find(Auth::id());
        $data = $this->form->getState();
        foreach ($data as $key => $value) {
            $user->{$key} = $value;
        }
        $user->save();

        Notification::make()
            ->title('Profil berhasil diperbarui!')
            ->success()
            ->send();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}

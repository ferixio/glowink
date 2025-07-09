<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as ActionsAction;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.admin.pages.settings';


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



}

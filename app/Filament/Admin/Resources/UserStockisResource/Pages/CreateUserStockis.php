<?php

namespace App\Filament\Admin\Resources\UserStockisResource\Pages;

use App\Filament\Admin\Resources\UserStockisResource;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUserStockis extends CreateRecord
{
    protected static string $resource = UserStockisResource::class;

    public function getTitle(): string
    {
        return 'Update Data Stockis';
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Get the selected user ID
        $userId = $data['id'] ?? null;

        if (!$userId) {
            Notification::make()
                ->title('Error')
                ->body('Please select a user to update.')
                ->danger()
                ->send();

            throw new \Exception('No user selected');
        }

        // Find the existing user
        $user = User::find($userId);

        if (!$user) {
            Notification::make()
                ->title('Error')
                ->body('Selected user not found.')
                ->danger()
                ->send();

            throw new \Exception('User not found');
        }

        // Update only the specific fields we want to change
        $user->isMitraBasic = false;
        $user->isMitraKarir = false;
        $user->isStockis = true;
        $user->provinsi = $data['provinsi'] ?? $user->provinsi;
        $user->kabupaten = $data['kabupaten'] ?? $user->kabupaten;
        $user->save();

        Notification::make()
            ->title('Berhasil')
            ->body('Mitra berhasil di update menjadi Stockis')
            ->success()
            ->send();

        return $user;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('update')
                ->label('Update')
                ->submit('update')
                ->color('primary')
                ->icon('heroicon-o-check'),
        ];
    }

}

<?php

namespace App\Filament\Admin\Resources\WithDrawResource\Pages;

use App\Filament\Admin\Resources\WithDrawResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditWithDraw extends EditRecord
{
    protected static string $resource = WithDrawResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Proses')
                ->label('Proses WithDraw')
                ->color('info')
                ->visible(fn() => $this->record->status === 'pending')
                ->action(function () {
                    $this->record->status = 'proses';
                    $this->record->save();
                    Notification::make()
                        ->title('Berhasil')
                        ->body('Status penarikan dana diubah menjadi proses.')
                        ->success()
                        ->send();
                    return redirect()->to($this->getResource()::getUrl('index'));
                }),
            Actions\Action::make('Tolak')
                ->label('Tolak WithDraw')
                ->color('danger')
                ->visible(fn() => $this->record->status !== 'ditolak')
                ->action(function () {
                    // If status was 'proses', we need to return the money to saldo_penghasilan
                    if ($this->record->status === 'proses') {
                        $user = $this->record->user;
                        $user->update([
                            'saldo_penghasilan' => $user->saldo_penghasilan + $this->record->nominal,
                            'saldo_withdraw' => $user->saldo_withdraw - $this->record->nominal,
                        ]);
                    }

                    $this->record->status = 'ditolak';
                    $this->record->save();
                    Notification::make()
                        ->title('Berhasil')
                        ->body('Status penarikan dana diubah menjadi ditolak.')
                        ->success()
                        ->send();
                    return redirect()->to($this->getResource()::getUrl('index'));
                }),
            Actions\Action::make('Selesai')
                ->label('Selesaikan WithDraw')
                ->color('success')
                ->visible(fn() => $this->record->status == 'proses')
                ->action(function () {
                    // Update user balance when withdrawal is completed
                    $user = $this->record->user;
                    $user->update([
                        'saldo_withdraw' => $user->saldo_withdraw - $this->record->nominal,
                    ]);

                    $this->record->status = 'selesai';
                    $this->record->save();
                    Notification::make()
                        ->title('Berhasil')
                        ->body('Status penarikan dana diubah menjadi selesai dan saldo withdraw telah diperbarui.')
                        ->success()
                        ->send();
                    return redirect()->to($this->getResource()::getUrl('index'));
                }),

        ];
    }

    protected function getFormActions(): array
    {
        return [

        ];
    }
}

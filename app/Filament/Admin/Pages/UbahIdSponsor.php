<?php

namespace App\Filament\Admin\Pages;

use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class UbahIdSponsor extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.admin.pages.ubah-id-sponsor';
    protected static ?string $title = 'Ubah ID Sponsor';
    protected static ?string $navigationLabel = 'Ubah ID Sponsor';

    public ?array $data = [];
    public array $selectedUserIds = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Pilih Multiple Users')
                    ->description('Pilih multiple users untuk diubah ID sponsor-nya sekaligus')
                    ->schema([
                        Select::make('selectedUserIds')
                            ->label('Pilih Users')
                            ->placeholder('Cari dan pilih multiple users')
                            ->getSearchResultsUsing(function (string $search): Collection {
                                if (strlen($search) < 2) {
                                    return collect();
                                }

                                return User::query()
                                    ->where('isAdmin', false)
                                    ->where(function ($query) use ($search) {
                                        $query->where('nama', 'like', "%{$search}%")
                                            ->orWhere('email', 'like', "%{$search}%")
                                            ->orWhere('id_mitra', 'like', "%{$search}%")
                                            ->orWhere('username', 'like', "%{$search}%");
                                    })
                                    ->limit(10)
                                    ->get()
                                    ->mapWithKeys(function ($user) {
                                        $label = "{$user->nama} ({$user->id_mitra})";
                                        if ($user->email) {
                                            $label .= " - {$user->email}";
                                        }
                                        return [$user->id => $label];
                                    });
                            })
                            ->searchable()
                            ->multiple()
                            ->reactive()
                            ->afterStateUpdated(function ($state) {
                                $this->selectedUserIds = $state ?? [];
                            }),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Section::make('Pilih Mitra Sponsor')
                    ->description('Pilih mitra untuk dijadikan sponsor untuk semua user yang dipilih')
                    ->schema([
                        Select::make('newSponsorId')
                            ->label('Pilih Mitra Sponsor')
                            ->placeholder('Cari mitra sponsor berdasarkan nama, email, atau ID mitra')
                            ->getSearchResultsUsing(function (string $search): Collection {
                                if (strlen($search) < 2) {
                                    return collect();
                                }

                                return User::query()
                                    ->where('isAdmin', false)
                                    ->where(function ($query) use ($search) {
                                        $query->where('nama', 'like', "%{$search}%")
                                            ->orWhere('email', 'like', "%{$search}%")
                                            ->orWhere('id_mitra', 'like', "%{$search}%")
                                            ->orWhere('username', 'like', "%{$search}%");
                                    })
                                    ->limit(10)
                                    ->get()
                                    ->mapWithKeys(function ($user) {
                                        $label = "{$user->nama} ({$user->id_mitra})";
                                        if ($user->email) {
                                            $label .= " - {$user->email}";
                                        }
                                        return [$user->id => $label];
                                    });
                            })
                            ->searchable()
                            ->required()
                            ->visible(fn() => !empty($this->selectedUserIds)),
                    ])
                    ->collapsible()
                    ->collapsed(false)
                    ->visible(fn() => !empty($this->selectedUserIds)),
            ])
            ->statePath('data');
    }

    public function updateSponsor(): void
    {
        $this->validate([
            'data.newSponsorId' => 'required|exists:users,id',
            'selectedUserIds' => 'required|array|min:1',
            'selectedUserIds.*' => 'exists:users,id',
        ]);

        $newSponsorId = $this->data['newSponsorId'];
        $selectedUserIds = $this->selectedUserIds;

        // Check if any selected user is trying to be their own sponsor
        if (in_array($newSponsorId, $selectedUserIds)) {
            Notification::make()
                ->title('Error')
                ->body('User tidak bisa menjadi sponsor untuk dirinya sendiri.')
                ->danger()
                ->send();
            return;
        }

        // Update id_sponsor for all selected users
        $updatedCount = User::whereIn('id', $selectedUserIds)
            ->update(['id_sponsor' => $newSponsorId]);

        if ($updatedCount > 0) {
            // Get sponsor info for notification
            $sponsor = User::find($newSponsorId);
            $sponsorName = $sponsor ? $sponsor->nama : 'Unknown';

            Notification::make()
                ->title('Berhasil')
                ->body("ID sponsor berhasil diubah untuk {$updatedCount} user menjadi {$sponsorName}.")
                ->success()
                ->send();

            // Reset form
            $this->selectedUserIds = [];
            $this->form->fill();
        } else {
            Notification::make()
                ->title('Error')
                ->body('Gagal mengubah ID sponsor.')
                ->danger()
                ->send();
        }
    }

    public function clearSelection(): void
    {
        $this->selectedUserIds = [];
        $this->form->fill();
    }
}

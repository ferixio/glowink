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
use Illuminate\Support\Facades\DB;

class UbahIdSponsor extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.admin.pages.ubah-id-sponsor';
    protected static ?string $title = 'Ubah ID Sponsor';
    protected static ?string $navigationLabel = 'Ubah ID Sponsor';
    // protected static bool $shouldRegisterNavigation = false;
    public ?array $data = [];
    public array $selectedUserIds = [];
    protected static ?int $navigationSort = 5;

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

        try {
            // Start transaction to ensure data consistency
            DB::beginTransaction();

            // Update id_sponsor for all selected users
            $updatedCount = User::whereIn('id', $selectedUserIds)
                ->update(['id_sponsor' => $newSponsorId]);

            if ($updatedCount > 0) {
                // Update jaringan_mitras table for all selected users
                $this->rebuildNetworkForUsers($selectedUserIds, $newSponsorId);

                // Get sponsor info for notification
                $sponsor = User::find($newSponsorId);
                $sponsorName = $sponsor ? $sponsor->nama : 'Unknown';

                DB::commit();

                Notification::make()
                    ->title('Berhasil')
                    ->body("ID sponsor berhasil diubah untuk {$updatedCount} user menjadi {$sponsorName}. Jaringan mitra telah diperbarui.")
                    ->success()
                    ->send();

                // Reset form
                $this->selectedUserIds = [];
                $this->form->fill();
            } else {
                DB::rollBack();
                Notification::make()
                    ->title('Error')
                    ->body('Gagal mengubah ID sponsor.')
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Error')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function clearSelection(): void
    {
        $this->selectedUserIds = [];
        $this->form->fill();
    }

    /**
     * Rebuild network structure for multiple users
     */
    private function rebuildNetworkForUsers(array $userIds, int $newSponsorId): void
    {
        foreach ($userIds as $userId) {
            $this->rebuildNetworkForUser($userId, $newSponsorId);
        }
    }

    /**
     * Rebuild network structure for a single user
     */
    private function rebuildNetworkForUser(int $userId, int $newSponsorId): void
    {
        // Delete all existing network relationships for this user
        DB::table('jaringan_mitras')->where('user_id', $userId)->delete();

        $currentSponsorId = $newSponsorId;
        $level = 1;
        $maxLevel = 10; // Maximum network depth

        // Build network structure from new sponsor up to 10 levels
        while ($currentSponsorId && $level <= $maxLevel) {
            // Check if sponsor exists
            $sponsor = User::find($currentSponsorId);
            if (!$sponsor) {
                break;
            }

            // Create network relationship
            DB::table('jaringan_mitras')->insert([
                'user_id' => $userId,
                'sponsor_id' => $currentSponsorId,
                'level' => $level,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Move to next level up
            $currentSponsorId = $sponsor->id_sponsor;
            $level++;
        }

        // Also rebuild network for all downlines of this user
        $this->rebuildNetworkForDownlines($userId);
    }

    /**
     * Rebuild network structure for all downlines of a user
     */
    private function rebuildNetworkForDownlines(int $userId): void
    {
        // Get all direct downlines
        $downlines = DB::table('jaringan_mitras')
            ->where('sponsor_id', $userId)
            ->where('level', 1)
            ->get();

        foreach ($downlines as $downline) {
            $this->rebuildNetworkForUser($downline->user_id, $userId);
        }
    }
}

<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\AktivasiPinResource\Pages;
use App\Models\AktivasiPin;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AktivasiPinResource extends Resource
{
    protected static ?string $model = AktivasiPin::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Aktivasi PIN';
    protected static ?string $breadcrumb = "Aktivasi PIN";
    protected static ?string $label = 'Aktivasi PIN';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('pin')
                    ->label('Detail')
                    ->html()
                    ->formatStateUsing(function ($state, $record) {
                        $status = $record->is_accept
                        ? "<span class='inline-flex items-center text-green-600 text-xs font-semibold'>
                        <svg xmlns='http://www.w3.org/2000/svg' class='h-4 w-4 mr-1' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12l2 2l4-4m6 2a9 9 0 11-18 0a9 9 0 0118 0z'/>
                        </svg>
                        Telah diaktivasi
                   </span>"
                        : "<span class='inline-flex items-center text-blue-600 text-xs font-semibold'>
                        <svg xmlns='http://www.w3.org/2000/svg' class='h-4 w-4 mr-1' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 18L18 6M6 6l12 12'/>
                        </svg>
                        Belum diaktivasi
                   </span>";

                        return "
                <div class='flex flex-col'>
                    <span class='font-bold text-gray-800'>PIN: {$record->pin}</span>
                    <span class='text-sm text-gray-600'>{$record->produk->nama}</span>
                    <div class='mt-1'>{$status}</div>
                </div>
            ";
                    })
                    ->searchable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('accept')
                    ->label('Aktivasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->button()
                    ->visible(fn(AktivasiPin $record) => !$record->is_accept)
                    ->action(function (AktivasiPin $record) {

                        event(new \App\Events\BonusAktivasiPin($record));
                        $record->update(['is_accept' => true]);

                        \Filament\Notifications\Notification::make()
                            ->title('PIN berhasil diaktivasi')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('view_bonus')
                    ->label('Lihat Bonus')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('info')
                    ->button()
                    ->modalHeading('Data Bonus Aktivasi Pin')
                    ->modalContent(function (AktivasiPin $record) {
                        $pembelianDetail = $record->pembelianDetail;
                        $pembelian = $pembelianDetail->pembelian;

                        $pembelianBonuses = $record->pembelianBonuses()->with('user')->get();

                        if ($pembelianBonuses->isEmpty()) {
                            return view('filament.components.no-data', [
                                'message' => 'Belum ada data bonus aktivasi pin untuk PIN ini.',
                            ]);
                        }

                        return view('filament.components.pembelian-bonus-table', [
                            'pembelianBonuses' => $pembelianBonuses,
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalSubmitActionLabel('')
                    ->modalCloseButton(null)
                    ->visible(fn(AktivasiPin $record) => $record->is_accept),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where(function ($query) {
            $query->where('user_id', auth()->id());
        })->orderByDesc('updated_at');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAktivasiPins::route('/'),
            'create' => Pages\CreateAktivasiPin::route('/create'),
            'edit' => Pages\EditAktivasiPin::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\User\Resources;

use App\Events\BonusAktivasiPin;
use App\Events\PembelianDetailAktivasi;
use App\Filament\User\Resources\PembelianDetailResource\Pages;
use App\Models\PembelianDetail;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PembelianDetailResource extends Resource
{
    protected static ?string $model = PembelianDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Aktivasi PIN';
    protected static ?string $breadcrumb = "Aktivasi PIN";
    protected static ?string $label = 'Aktivasi PIN';

    protected static ?int $navigationSort = 3;
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
            ->columns([

                Tables\Columns\TextColumn::make('nama_produk')
                    ->label('Nama Produk'),
                Tables\Columns\TextColumn::make('jml_beli')
                    ->label('Quantity'),
                Tables\Columns\TextColumn::make('pin')
                    ->label('PIN'),
                Tables\Columns\TextColumn::make('is_accepted')
                    ->label('Status')

                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        '1' => 'success',
                        '0' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        '1' => 'Diterima',
                        '0' => 'Belum Diterima',
                        default => 'Unknown',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                //
            ])
            ->actions([

                Tables\Actions\Action::make('accept')
                    ->label('Terima')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->button()
                    ->action(function (PembelianDetail $record) {
                        $record->update(['is_accepted' => true]);

                        event(new PembelianDetailAktivasi($record, auth()->user()));

                        \Filament\Notifications\Notification::make()
                            ->title('PIN berhasil diterima')
                            ->success()
                            ->send();
                    })
                    ->disabled(fn(PembelianDetail $record) => $record->is_accepted == true),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([

                    Tables\Actions\BulkAction::make('accept_selected')
                        ->label('Terima yang Dipilih')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update(['is_accepted' => true]);
                                event(new BonusAktivasiPin($record, auth()->user()));
                                // Trigger event untuk menambah saldo_penghasilan
                                // event(new PembelianDetailAktivasi($record, auth()->user()));
                            });

                        }),

                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('pembelian', function ($query) {
                $query->where('user_id', auth()->id())
                    ->where('pin', '!=', null)
                    ->orWhere('id_sponsor', auth()->id());
            })
            ->orderByDesc('updated_at');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembelianDetails::route('/'),
            'create' => Pages\CreatePembelianDetail::route('/create'),
            'edit' => Pages\EditPembelianDetail::route('/{record}/edit'),
        ];
    }

    public static function getRecordUrl($record): ?string
    {
        return null; // Disable row click navigation
    }
}

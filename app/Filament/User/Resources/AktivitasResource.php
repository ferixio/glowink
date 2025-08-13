<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\AktivitasResource\Pages;
use App\Models\Aktivitas;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AktivitasResource extends Resource
{
    protected static ?string $model = Aktivitas::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                Tables\Columns\TextColumn::make('judul')
                    ->label('Aktivitas')
                    ->formatStateUsing(function ($record) {
                        return '
            <div style="line-height:1.4">
                <div style="font-size: 0.75rem; color: #6b7280; font-weight: 500;">' . e(format_tanggal_indonesia($record->created_at)) . '</div>
                <div style="font-weight: 600; color: #111827;">' . e($record->judul) . '</div>
                <div style="font-size: 0.85rem; color: #4b5563;">' . e($record->keterangan) . '</div>
            </div>
        ';
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('status')
                    ->label('')
                    ->formatStateUsing(function ($record) {
                        $nominalText = $record->nominal !== null
                        ? 'Rp ' . number_format($record->nominal, 0, ',', '.')
                        : '<span style="color:#6b7280; font-size:0.85rem;">Tidak ada nominal</span>';

                        return '
            <div style="line-height:1.4">
                <div style="font-size: 0.85rem; font-weight: 500; color: ' . ($record->status === 'Selesai' ? '#059669' : '#b91c1c') . ';">' . e($record->status) . '</div>
                <div style="font-size: 1rem; font-weight: 700; color: #111827;">' . $nominalText . '</div>
            </div>
        ';
                    })
                    ->html(),

            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAktivitas::route('/'),
            'create' => Pages\CreateAktivitas::route('/create'),
            'edit' => Pages\EditAktivitas::route('/{record}/edit'),
        ];
    }
}

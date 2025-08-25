<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\AktivitasResource\Pages;
use App\Models\Aktivitas;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AktivitasResource extends Resource
{
    protected static ?string $model = Aktivitas::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 6;
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
                    ->getStateUsing(function ($record) {
                        return '
            <div style="line-height:1.4">
                <div style="font-size: 0.75rem; color: #6b7280; font-weight: 500;">' . e(format_tanggal_indonesia($record->created_at)) . '</div>
                <div style="font-weight: 600; color: #111827;">' . e($record->judul) . '</div>
                <div style="font-size: 0.85rem; color: ' . ($record->tipe === 'plus' ? '#059669' : ($record->tipe === 'minus' ? '#dc2626' : '#4b5563')) . ';">' . e($record->keterangan) . '</div>
            </div>
        ';
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('status')
                    ->label('')
                    ->getStateUsing(function ($record) {
                        $nominalText = $record->nominal !== null
                        ? ($record->tipe === 'plus' ? '+' : ($record->tipe === 'minus' ? '-' : '')) . number_format($record->nominal, 0, ',', '.')

                        : '<span style=""> </span>';

                        return '
            <div style="line-height:1.4">
                <div style="font-size: 0.85rem; font-weight: 500; color: ' . ('#059669') . ';">' . e($record->status) . '</div>
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())->orderByDesc('updated_at');
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

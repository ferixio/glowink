<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LaporanDevidenHarianResource\Pages;
use App\Models\DevidenHarian;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LaporanDevidenHarianResource extends Resource
{
    protected static ?string $model = DevidenHarian::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationLabel = 'Laporan Deviden Harian';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'Laporan - Laporan';
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
                Tables\Columns\TextColumn::make('tanggal_deviden')
                    ->label('Tanggal Deviden')
                    ->date('d-m-Y'),
                Tables\Columns\TextColumn::make('total_member')
                    ->label('Total Member'),
                Tables\Columns\TextColumn::make('omzet_aktivasi')
                    ->label('Omzet Aktivasi')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('omzet_ro_basic')
                    ->label('Omzet RO Basic')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('deviden_diterima')
                    ->label('Deviden Diterima')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->orderByDesc('tanggal_deviden');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanDevidenHarians::route('/'),
            'create' => Pages\CreateLaporanDevidenHarian::route('/create'),
            'edit' => Pages\EditLaporanDevidenHarian::route('/{record}/edit'),
        ];
    }
}

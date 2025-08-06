<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PenghasilanResource\Pages;
use App\Models\Penghasilan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PenghasilanResource extends Resource
{
    protected static ?string $model = Penghasilan::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Laporan - Laporan';
    protected static ?string $navigationLabel = 'Laporan Penghasilan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('tgl_dapat_bonus')
                    ->required()
                    ->date(),
                Forms\Components\TextInput::make('keterangan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nominal_bonus')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('kategori_bonus')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id'),
                Tables\Columns\TextColumn::make('tgl_dapat_bonus'),
                Tables\Columns\TextColumn::make('keterangan'),
                Tables\Columns\TextColumn::make('nominal_bonus'),
                Tables\Columns\TextColumn::make('kategori_bonus'),
                Tables\Columns\TextColumn::make('status_qr'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kategori_bonus')
                    ->options(Penghasilan::pluck('kategori_bonus', 'kategori_bonus'))
                    ->label('Kategori Bonus'),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
        ;

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
            'index' => Pages\ListPenghasilans::route('/'),
            'create' => Pages\CreatePenghasilan::route('/create'),
            'edit' => Pages\EditPenghasilan::route('/{record}/edit'),
        ];
    }
}

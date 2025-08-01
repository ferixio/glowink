<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LevelKarirResource\Pages;
use App\Models\LevelKarir;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LevelKarirResource extends Resource
{
    protected static ?string $model = LevelKarir::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
protected static ?int $navigationSort = 3;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_level')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('poin_reward')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('minimal_RO_QR')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('angka_deviden')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('jumlah_mitra_level_ini')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_level'),
                TextColumn::make('poin_reward'),
                TextColumn::make('minimal_RO_QR')->label('Minimal RO QR'),
                TextColumn::make('angka_deviden'),
                // TextColumn::make('jumlah_mitra_level_ini'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListLevelKarirs::route('/'),
            'create' => Pages\CreateLevelKarir::route('/create'),
            'edit' => Pages\EditLevelKarir::route('/{record}/edit'),
        ];
    }
}

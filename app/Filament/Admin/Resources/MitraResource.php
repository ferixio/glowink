<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MitraResource\Pages;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MitraResource extends Resource
{
    protected static ?string $model = User::class;

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
                Tables\Columns\TextColumn::make('id_mitra')
                    ->label('ID Mitra')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Mitra')
                    ->searchable(),
                Tables\Columns\TextColumn::make('plan_karir_sekarang')
                    ->label('Plan Karir')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_telp')
                    ->label('No. Telepon'),
                Tables\Columns\TextColumn::make('plan_karir_sekarang')
                    ->label('Plan Karir'),
                Tables\Columns\TextColumn::make('saldo_penghasilan')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('poin_reward')
                    ->numeric(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('plan_karir_sekarang')
                    ->options([
                        'bronze' => 'Bronze',
                        'silver' => 'Silver',
                        'gold' => 'Gold',
                        'platinum' => 'Platinum',
                        'titanium' => 'Titanium',
                        'ambassador' => 'Ambassador',
                        'chairman' => 'Chairman',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListMitras::route('/'),
            'create' => Pages\CreateMitra::route('/create'),
            'edit' => Pages\EditMitra::route('/{record}/edit'),
        ];
    }
}

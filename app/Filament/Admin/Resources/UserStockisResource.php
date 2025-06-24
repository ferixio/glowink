<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserStockisResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserStockisResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'User Stockis';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Information')
                    ->schema([
                        Forms\Components\TextInput::make('id_mitra')
                            ->label('ID Mitra')
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('username')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrated(fn($state) => filled($state))
                            ->dehydrateStateUsing(fn($state) => Hash::make($state)),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('isAdmin')
                                    ->label('Admin')
                                    ->default(false),
                                Forms\Components\Toggle::make('isStockis')
                                    ->label('Stockis')
                                    ->default(false),
                                Forms\Components\Toggle::make('isMitraBasic')
                                    ->label('Mitra Basic')
                                    ->default(false),
                                Forms\Components\Toggle::make('isMitraKarir')
                                    ->label('Mitra Karir')
                                    ->default(false),
                            ]),
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->label('Nama Lengkap'),
                    ]),

                Forms\Components\Section::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('provinsi')->required(),
                        Forms\Components\TextInput::make('kabupaten')->required(),
                        Forms\Components\Textarea::make('alamat')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('no_telp')
                            ->label('No. Telepon')
                            ->tel()
                            ->required(),
                    ]),

                Forms\Components\Section::make('Bank Information')
                    ->schema([
                        Forms\Components\TextInput::make('no_rek')
                            ->label('No. Rekening')
                            ->required(),
                        Forms\Components\TextInput::make('nama_rekening')
                            ->required(),
                        Forms\Components\TextInput::make('bank')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Sponsor Information')
                    ->schema([
                        Forms\Components\Select::make('id_sponsor')
                            ->relationship('sponsor', 'nama')
                            ->label('Sponsor'),
                        Forms\Components\TextInput::make('group_sponsor')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Career Information')
                    ->schema([
                        Forms\Components\TextInput::make('plan_karir_sekarang')
                            ->required(),
                        Forms\Components\TextInput::make('next_plan_karir')
                            ->required(),
                        Forms\Components\TextInput::make('next_poin_karir')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ToggleColumn::make('isAdmin')
                    ->label('Admin'),
                Tables\Columns\TextColumn::make('id_mitra')
                    ->label('ID Mitra')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Mitra')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
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
                //
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
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('isStockis', true);
    }
    public static function getNavigationLabel(): string
    {
        return 'User Stockis';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserStockis::route('/'),
            'create' => Pages\CreateUserStockis::route('/create'),
            'edit' => Pages\EditUserStockis::route('/{record}/edit'),
        ];
    }
}

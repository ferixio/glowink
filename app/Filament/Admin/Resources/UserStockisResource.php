<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserStockisResource\Pages;
use App\Models\User;
use App\Services\LocationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserStockisResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = null;

    public static function getNavigationLabel(): string
    {
        return 'Data Stockis';
    }

    protected static ?string $navigationGroup = "Master Data";
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Update Data Stockis')
                    ->description('Pilih Mitra Basic atau Mitra Karir untuk mengubahnya menjadi Stockis')
                    ->schema([

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('id')
                                    ->label('Pilih Mitra')
                                    ->options(function () {
                                        return User::where('isStockis', false)
                                            ->pluck('nama', 'id')
                                            ->toArray();
                                    })
                                    ->searchable()
                                    ->required()
                                    ->disabled(fn($record) => $record !== null)
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $user = User::find($state);
                                            if ($user) {
                                                $set('nama', $user->nama);
                                                $set('provinsi', $user->provinsi);
                                                $set('kabupaten', $user->kabupaten);
                                            }
                                        }
                                    }),

                                Forms\Components\TextInput::make('nama')
                                    ->required()
                                    ->label('Nama Lengkap')
                                    ->disabled(),
                                Forms\Components\Select::make('provinsi')
                                    ->label('Provinsi')
                                    ->options(LocationService::getProvinces())
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn() => null),
                                Forms\Components\Select::make('kabupaten')
                                    ->label('Kabupaten')
                                    ->options(function (callable $get) {
                                        $provinceId = $get('provinsi');
                                        if (!$provinceId) {
                                            return [];
                                        }

                                        $regencies = LocationService::getRegenciesByProvince($provinceId);
                                        $options = [];
                                        foreach ($regencies as $regency) {
                                            $options[$regency['name']] = $regency['name'];
                                        }
                                        return $options;
                                    })
                                    ->searchable()
                                    ->required()
                                    ->disabled(fn(callable $get) => !$get('provinsi')),
                                Forms\Components\Placeholder::make('current_status')
                                    ->label('Status Saat Ini')
                                    ->content(function (callable $get) {
                                        $userId = $get('id');
                                        if ($userId) {
                                            $user = User::find($userId);
                                            if ($user) {
                                                $status = [];
                                                if ($user->status_qr) {
                                                    $status[] = 'Mitra Karir';
                                                } else {
                                                    $status[] = 'Mitra Basic';
                                                }

                                                return implode(', ', $status) ?: 'Tidak ada status';
                                            }
                                        }
                                        return 'Pilih mitra terlebih dahulu';
                                    }),
                                Forms\Components\Placeholder::make('new_status')
                                    ->label('Status Baru')
                                    ->content('Stockis'),
                            ]),

                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Mitra')
                    ->searchable(),
                Tables\Columns\TextColumn::make('provinsi')
                    ->label('Provinsi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kabupaten')
                    ->label('Kabupaten')
                    ->searchable(),

                Tables\Columns\TextColumn::make('saldo_penghasilan')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('poin_reward')
                    ->numeric(),
                Tables\Columns\TextColumn::make('no_telp')
                    ->label('No. Telepon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('plan_karir_sekarang')
                    ->label('Plan Karir')
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
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
                // No bulk actions for update-only resource
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
            $query->where('isStockis', true)->orderByDesc('updated_at');
        });
    }

    public static function getCreateButtonLabel(): string
    {
        return 'Update';
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

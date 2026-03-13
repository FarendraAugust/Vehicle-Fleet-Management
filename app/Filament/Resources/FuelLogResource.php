<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FuelLogResource\Pages;
use App\Models\FuelLog;

use Filament\Forms\Form;
use Filament\Forms\Components\{
    DatePicker,
    Grid,
    Section,
    Select,
    TextInput
};

use Filament\Resources\Resource;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables;

class FuelLogResource extends Resource
{
    protected static ?string $model = FuelLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-fire';

    protected static ?string $navigationLabel = 'Fuel Logs';

    protected static ?string $navigationGroup = 'Fleet Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Fuel Information')
                    ->description('Record fuel usage for a vehicle')
                    ->icon('heroicon-o-fire')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                Select::make('vehicle_id')
                                    ->label('Vehicle')
                                    ->relationship('vehicle', 'plate_number')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                DatePicker::make('date')
                                    ->label('Fuel Date')
                                    ->required(),

                            ]),

                    ])
                    ->columns(1),

                Section::make('Fuel Details')
                    ->description('Fuel amount and cost')
                    ->icon('heroicon-o-banknotes')
                    ->schema([

                        Grid::make(3)
                            ->schema([

                                TextInput::make('fuel_amount')
                                    ->label('Fuel Amount')
                                    ->numeric()
                                    ->suffix('Liter')
                                    ->required(),

                                TextInput::make('cost')
                                    ->label('Fuel Cost')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required(),

                            ]),

                    ])
                    ->columns(1),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->recordAction(null)
            ->columns([

                TextColumn::make('vehicle.plate_number')
                    ->label('Vehicle')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('fuel_amount')
                    ->label('Fuel')
                    ->suffix(' L')
                    ->sortable(),

                TextColumn::make('cost')
                    ->label('Cost')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Logged At')
                    ->dateTime(),

            ])

            ->filters([
                //
            ])

            ->actions([
            ])

            ->bulkActions([
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFuelLogs::route('/'),
            'create' => Pages\CreateFuelLog::route('/create'),
        ];
    }
}

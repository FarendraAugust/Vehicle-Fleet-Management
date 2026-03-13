<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleUsageResource\Pages;
use App\Models\VehicleUsage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class VehicleUsageResource extends Resource
{
    protected static ?string $model = \App\Models\Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';

    protected static ?string $navigationLabel = 'Vehicle Returns';

    protected static ?string $navigationGroup = 'Fleet Operations';

    protected static ?int $navigationSort = 6;

    public static function getPluralModelLabel(): string
    {
        return 'Vehicle Returns';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->recordAction(null)
            ->columns([

                TextColumn::make('vehicle.plate_number')
                    ->label('Vehicle')
                    ->icon('heroicon-m-truck'),

                TextColumn::make('vehicle.brand')
                    ->label('Brand'),

                TextColumn::make('driver.name')
                    ->label('Driver')
                    ->icon('heroicon-m-user'),

                TextColumn::make('destination')
                    ->label('Destination')
                    ->icon('heroicon-m-map-pin'),

                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->dateTime(),

                TextColumn::make('end_date')
                    ->label('End Date')
                    ->dateTime(),

            ])
            ->actions([
                Tables\Actions\Action::make('return_vehicle')
                    ->label('Return Vehicle')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->color('success')

                    ->requiresConfirmation()

                    ->modalHeading('Return Vehicle')

                    ->modalDescription(function ($record) {

                        if (now()->lt($record->end_date)) {
                            return 'This booking has not reached its scheduled end time. Are you sure you want to return the vehicle early?';
                        }

                        return 'Confirm vehicle return.';
                    })

                    ->modalSubmitActionLabel('Continue')

                    ->form([

                        TextInput::make('start_odometer')
                            ->label('Start Odometer (km)')
                            ->disabled()
                            ->default(fn($record) => $record->vehicle->current_odometer),

                        TextInput::make('end_odometer')
                            ->label('End Odometer (km)')
                            ->numeric()
                            ->required(),

                    ])

                    ->action(function ($record, $data) {

                        $distance = $data['end_odometer'] - $record->vehicle->current_odometer;

                        \App\Models\VehicleUsage::create([
                            'vehicle_id' => $record->vehicle_id,
                            'booking_id' => $record->id,
                            'start_odometer' => $record->vehicle->current_odometer,
                            'end_odometer' => $data['end_odometer'],
                        ]);

                        // booking completed
                        $record->update([
                            'status' => 'completed'
                        ]);

                        // vehicle available again
                        $record->vehicle()->update([
                            'status' => 'available',
                            'current_odometer' => $data['end_odometer']
                        ]);
                    })
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return \App\Models\Booking::query()
            ->with(['vehicle', 'driver'])
            ->where('status', 'approved');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicleUsages::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceLogResource\Pages;
use App\Filament\Resources\ServiceLogResource\RelationManagers;
use App\Models\ServiceLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceLogResource extends Resource
{
    protected static ?string $model = ServiceLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Service Logs';

    protected static ?string $navigationGroup = 'Fleet Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('Service Information')
                    ->description('Record vehicle maintenance or repair')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->schema([

                        Forms\Components\Select::make('vehicle_id')
                            ->label('Vehicle')
                            ->relationship('vehicle', 'plate_number')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\DatePicker::make('service_date')
                            ->label('Service Date')
                            ->required(),

                        Forms\Components\Textarea::make('description')
                            ->label('Service Description')
                            ->placeholder('Example: Engine oil replacement')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('cost')
                            ->label('Service Cost')
                            ->numeric()
                            ->prefix('Rp')
                            ->columnSpanFull(),

                    ])
                    ->columns(2)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('vehicle.plate_number')
                    ->label('Vehicle')
                    ->icon('heroicon-m-truck')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('service_date')
                    ->label('Service Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(40),

                Tables\Columns\TextColumn::make('cost')
                    ->label('Cost')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('odometer')
                    ->label('Odometer')
                    ->suffix(' km')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Logged At')
                    ->dateTime('d M Y')
                    ->toggleable(),

            ])
            ->filters([

                Tables\Filters\SelectFilter::make('vehicle')
                    ->relationship('vehicle', 'plate_number')
                    ->label('Vehicle'),

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
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
            'index' => Pages\ListServiceLogs::route('/'),
            'create' => Pages\CreateServiceLog::route('/create'),
            'edit' => Pages\EditServiceLog::route('/{record}/edit'),
        ];
    }
}

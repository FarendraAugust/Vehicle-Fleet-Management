<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleResource\Pages;
use App\Filament\Resources\VehicleResource\RelationManagers;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Enums\ActionsPosition;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Vehicles';

    protected static ?string $navigationGroup = 'Fleet Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Vehicle Information')
                    ->schema([

                        FileUpload::make('image')
                            ->label('Vehicle Image')
                            ->image()
                            ->disk('public')
                            ->directory('vehicles')
                            ->visibility('public')
                            ->imagePreviewHeight('200')
                            ->openable()
                            ->downloadable()
                            ->required(),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('plate_number')
                                    ->label('Plate Number')
                                    ->placeholder('AG 3157 CE')
                                    ->required()
                                    ->maxLength(20)
                                    ->unique(ignoreRecord: true),

                                TextInput::make('year')
                                    ->label('Year')
                                    ->numeric()
                                    ->minValue(1900)
                                    ->maxValue(now()->year)
                                    ->placeholder(now()->year)
                                    ->required(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('brand')
                                    ->required()
                                    ->placeholder('Toyota')
                                    ->label('Brand'),

                                TextInput::make('model')
                                    ->required()
                                    ->placeholder('Hilux')
                                    ->label('Model'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Select::make('vehicle_type')
                                    ->label('Vehicle Type')
                                    ->options([
                                        'people' => 'People Transport',
                                        'goods' => 'Goods Transport',
                                    ])
                                    ->native(false)
                                    ->required(),

                                Select::make('ownership')
                                    ->label('Ownership')
                                    ->options([
                                        'company' => 'Company',
                                        'rental' => 'Rental',
                                    ])
                                    ->native(false)
                                    ->required(),
                            ]),

                        Select::make('region_id')
                            ->label('Region')
                            ->relationship('region', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Hidden::make('status')
                            ->default('available'),

                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                ImageColumn::make('image')
                    ->disk('public')
                    ->height(120)
                    ->square()
                    ->extraImgAttributes([
                        'class' => 'rounded-lg object-cover'
                    ]),

                TextColumn::make('plate_number')
                    ->label('Plate')
                    ->weight('bold')
                    ->size('sm')
                    ->searchable(),

                TextColumn::make('brand')
                    ->label('Brand')
                    ->size('xs')
                    ->color('gray')
                    ->icon('heroicon-m-building-office')
                    ->searchable(),

                TextColumn::make('model')
                    ->size('xs')
                    ->color('gray')
                    ->icon('heroicon-m-cog-6-tooth')
                    ->searchable(),

                TextColumn::make('year')
                    ->size('xs')
                    ->color('gray')
                    ->icon('heroicon-m-calendar')
                    ->searchable(),

                BadgeColumn::make('vehicle_type')
                    ->label('Type')
                    ->size('sm')
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->colors([
                        'warning' => 'people',
                        'success' => 'goods',
                    ]),

                BadgeColumn::make('ownership')
                    ->label('Owner')
                    ->size('sm')
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->colors([
                        'primary' => 'company',
                        'info' => 'rental',
                    ]),

                TextColumn::make('region.name')
                    ->label('Region')
                    ->size('xs')
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->icon('heroicon-m-map-pin'),

                BadgeColumn::make('status')
                    ->size('sm')
                    ->formatStateUsing(fn($state) => str($state)->replace('_', ' ')->title())
                    ->colors([
                        'success' => 'available',
                        'warning' => 'in_use',
                        'danger' => 'maintenance',
                    ]),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('vehicle_type')
                    ->options([
                        'people' => 'People',
                        'goods' => 'Goods',
                    ]),

                Tables\Filters\SelectFilter::make('ownership')
                    ->options([
                        'company' => 'Company',
                        'rental' => 'Rental',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'in_use' => 'In Use',
                        'maintenance' => 'Maintenance',
                    ]),
            ])

            ->searchable()

            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}

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
                                    ->uppercase()
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
            ->contentGrid([
                'sm' => 1,
                'md' => 2,
                'lg' => 3,
                'xl' => 4,
                '2xl' => 5,
            ])

            ->columns([
                Stack::make([

                    ImageColumn::make('image')
                        ->disk('public')
                        ->height(150)
                        ->width('100%')
                        ->alignCenter(),

                    Stack::make([

                        Split::make([
                            TextColumn::make('brand')
                                ->weight('bold')
                                ->size(TextColumn\TextColumnSize::Large)
                                ->alignCenter(),

                            TextColumn::make('model')
                                ->color('gray')
                                ->alignCenter(),
                        ]),

                        TextColumn::make('plate_number')
                            ->label('Plate')
                            ->icon('heroicon-m-truck')
                            ->weight('bold')
                            ->alignCenter(),

                    ])->space(1),

                    Split::make([
                        BadgeColumn::make('vehicle_type')
                            ->label('Type')
                            ->colors([
                                'primary' => 'people',
                                'warning' => 'goods',
                            ])
                            ->alignCenter(),

                        BadgeColumn::make('ownership')
                            ->label('Ownership')
                            ->colors([
                                'success' => 'company',
                                'gray' => 'rental',
                            ])
                            ->alignCenter(),
                    ]),

                    Stack::make([

                        TextColumn::make('region.name')
                            ->icon('heroicon-m-map-pin')
                            ->label('Region')
                            ->alignCenter(),

                        TextColumn::make('year')
                            ->icon('heroicon-m-calendar')
                            ->label('Year')
                            ->alignCenter(),

                        TextColumn::make('current_odometer')
                            ->icon('heroicon-m-chart-bar')
                            ->numeric()
                            ->suffix(' km')
                            ->alignCenter(),

                    ])->space(1),

                    BadgeColumn::make('status')
                        ->alignCenter()
                        ->label('Status')
                        ->colors([
                            'success' => 'available',
                            'warning' => 'in_use',
                            'danger' => 'maintenance',
                        ])
                        ->formatStateUsing(fn($state) => ucfirst(str_replace('_', ' ', $state)))
                        ->alignCenter(),

                ])
                    ->space(2)

                    ->extraAttributes(fn($record) => [
                        'class' => '
                    p-6
                    hover:shadow-xl
                    hover:-translate-y-1
                    transition
                    min-h-[360px]
                '
                            .
                            match ($record->status) {
                                'available' => ' border-green-500',
                                'maintenance' => ' border-red-500',
                                default => ' border-yellow-400',
                            }
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])

            ->recordAction('view')

            ->paginated(false);
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

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FuelLogResource\Pages;
use App\Models\FuelLog;
use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Forms\Components\{
    DatePicker,
    Grid,
    Placeholder,
    Section,
    Select,
    TextInput
};

use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

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
                    ->collapsible()
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                Select::make('vehicle_id')
                                    ->label('Vehicle')
                                    ->relationship('vehicle', 'plate_number')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->placeholder('Select a vehicle')

                                    ->getOptionLabelFromRecordUsing(function ($record) {
                                        return new HtmlString("
                                        <div style='display:flex; align-items:center; gap:12px'>
                                            <img src='" . asset('storage/' . ($record->image ?? 'default-car.png')) . "' 
                                                onerror=\"this.src='" . asset('images/default-car.png') . "'\"
                                                style='width:50px; height:50px; object-fit:cover; border-radius:8px;'>
                                            <div>
                                                <div style='font-weight:600'>
                                                    {$record->plate_number}
                                                </div>
                                                <div style='font-size:12px; color:gray'>
                                                    {$record->brand} - {$record->model}
                                                </div>
                                            </div>
                                        </div>
                                        ");
                                    })

                                    ->getSearchResultsUsing(function (string $search) {
                                        return \App\Models\Vehicle::query()
                                            ->where('plate_number', 'like', "%{$search}%")
                                            ->orWhere('brand', 'like', "%{$search}%")
                                            ->limit(10)
                                            ->get()
                                            ->mapWithKeys(function ($record) {
                                                return [
                                                    $record->id => new HtmlString("
                                                        <div style='display:flex; align-items:center; gap:12px'>
                                                            <img src='" . asset('storage/' . ($record->image ?? 'default-car.png')) . "' 
                                                                onerror=\"this.src='" . asset('images/default-car.png') . "'\"
                                                                style='width:50px; height:50px; object-fit:cover; border-radius:8px;'>
                                                            <div>
                                                                <div style='font-weight:600'>
                                                                    {$record->plate_number}
                                                                </div>
                                                                <div style='font-size:12px; color:gray'>
                                                                    {$record->brand} - {$record->model}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    ")
                                                ];
                                            })
                                            ->toArray();
                                    })
                                    ->allowHtml()
                                    ->helperText('Choose a vehicle'),

                                DatePicker::make('date')
                                    ->label('Fuel Date')
                                    ->required()
                                    ->default(now())
                                    ->maxDate(now())
                                    ->displayFormat('d M Y')
                                    ->native(false)
                                    ->closeOnDateSelection()
                                    ->prefixIcon('heroicon-m-calendar-days')
                                    ->helperText('Select the date when the refueling occurred')

                            ]),

                    ]),

                Section::make('Fuel Details')
                    ->description('Enter fuel amount and cost')
                    ->icon('heroicon-o-banknotes')
                    ->collapsible()
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                TextInput::make('fuel_amount')
                                    ->label('Fuel Amount')
                                    ->numeric()
                                    ->suffix('L')
                                    ->placeholder('e.g. 25')
                                    ->required()
                                    ->minValue(1)
                                    ->step(0.1)
                                    ->live(debounce: 500)
                                    ->helperText('Enter fuel in liters'),

                                TextInput::make('cost')
                                    ->label('Fuel Cost')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->placeholder('e.g. 300000')
                                    ->required()
                                    ->minValue(0)
                                    ->live(debounce: 500)
                                    ->helperText('Total cost of fuel'),

                            ]),

                    ]),

                Section::make('Summary')
                    ->description('Quick overview before saving')
                    ->icon('heroicon-o-chart-bar')
                    ->collapsible()
                    ->schema([

                        Placeholder::make('cost_per_liter')
                            ->label('Cost per Liter')
                            ->content(function ($get) {
                                $fuel = (float) $get('fuel_amount');
                                $cost = (float) $get('cost');

                                if ($fuel > 0) {
                                    return 'Rp ' . number_format($cost / $fuel, 0, ',', '.') . ' / L';
                                }

                                return '-';
                            }),

                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->recordAction(null)
            ->striped()
            ->columns([

                TextColumn::make('vehicle.plate_number')
                    ->label('Vehicle')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-m-truck')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Date')
                    ->date('d M Y')
                    ->icon('heroicon-m-calendar-days')
                    ->sortable(),

                TextColumn::make('fuel_amount')
                    ->label('Fuel')
                    ->suffix(' L')
                    ->icon('heroicon-m-beaker')
                    ->color(fn($state) => $state > 50 ? 'danger' : 'success')
                    ->sortable(),

                TextColumn::make('cost')
                    ->label('Cost')
                    ->money('IDR', true)
                    ->icon('heroicon-m-banknotes')
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Logged At')
                    ->since()
                    ->tooltip(fn($record) => $record->created_at?->format('d M Y H:i'))
                    ->icon('heroicon-m-clock'),

            ])

            ->filters([

                // ✅ Filter by vehicle
                SelectFilter::make('vehicle_id')
                    ->label('Vehicle')
                    ->relationship('vehicle', 'plate_number')
                    ->searchable()
                    ->preload(),

                // ✅ Filter tanggal (kayak report)
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')->label('From Date'),
                        DatePicker::make('until')->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when(
                                $data['from'],
                                fn($q) => $q->whereDate('date', '>=', $data['from'])
                            )
                            ->when(
                                $data['until'],
                                fn($q) => $q->whereDate('date', '<=', $data['until'])
                            );
                    }),

            ])

            ->actions([
                DeleteAction::make(),
            ])

            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])

            ->defaultSort('date', 'desc');
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

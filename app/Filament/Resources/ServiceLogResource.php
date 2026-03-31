<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceLogResource\Pages;
use App\Filament\Resources\ServiceLogResource\RelationManagers;
use App\Models\ServiceLog;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter as FiltersFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

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

                Section::make('Service Information')
                    ->description('Record vehicle maintenance or repair')
                    ->icon('heroicon-o-wrench-screwdriver')
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

                                DatePicker::make('service_date')
                                    ->label('Service Date')
                                    ->required()
                                    ->default(now())
                                    ->maxDate(now())
                                    ->displayFormat('d M Y')
                                    ->native(false)
                                    ->closeOnDateSelection()
                                    ->prefixIcon('heroicon-m-calendar-days'),

                            ]),

                    ]),

                Section::make('Service Details')
                    ->description('Enter maintenance details')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->schema([

                        TextInput::make('cost')
                            ->label('Service Cost')
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('e.g. 500000')
                            ->required()
                            ->minValue(0)
                            ->live(debounce: 500)
                            ->helperText('Total service cost'),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->placeholder('e.g. Oil change, brake service')
                            ->rows(3)
                            ->columnSpanFull()
                            ->required(),

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

                TextColumn::make('service_date')
                    ->label('Service Date')
                    ->date('d M Y')
                    ->icon('heroicon-m-calendar-days')
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(35)
                    ->tooltip(fn($record) => $record->description)
                    ->icon('heroicon-m-document-text'),

                TextColumn::make('cost')
                    ->label('Cost')
                    ->money('IDR', true)
                    ->icon('heroicon-m-banknotes')
                    ->color(
                        fn($state) =>
                        $state > 1000000 ? 'danger' : ($state > 500000 ? 'warning' : 'success')
                    )
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Logged At')
                    ->since()
                    ->tooltip(fn($record) => $record->created_at?->format('d M Y H:i'))
                    ->icon('heroicon-m-clock'),

            ])

            ->filters([

                SelectFilter::make('vehicle_id')
                    ->label('Vehicle')
                    ->relationship('vehicle', 'plate_number')
                    ->searchable()
                    ->preload(),

                FiltersFilter::make('date_range')
                    ->form([
                        DatePicker::make('from')->label('From Date'),
                        DatePicker::make('until')->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when(
                                $data['from'],
                                fn($q) => $q->whereDate('service_date', '>=', $data['from'])
                            )
                            ->when(
                                $data['until'],
                                fn($q) => $q->whereDate('service_date', '<=', $data['until'])
                            );
                    }),

            ])

            ->actions([])

            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])

            ->defaultSort('service_date', 'desc');
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

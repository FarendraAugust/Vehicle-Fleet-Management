<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleBookingReportResource\Pages;
use App\Models\Booking;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Carbon\Carbon;

use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Exports\VehicleBookingReportExport;

class VehicleBookingReportResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'Vehicle Booking Report';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return $table

            ->columns([

                TextColumn::make('id')
                    ->label('Booking ID')
                    ->sortable(),

                TextColumn::make('vehicle.plate_number')
                    ->label('Plate Number')
                    ->searchable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('vehicle.brand')
                    ->label('Brand'),

                TextColumn::make('vehicle.model')
                    ->label('Model'),

                TextColumn::make('vehicle.vehicle_type')
                    ->label('Vehicle Type')
                    ->badge(),

                TextColumn::make('vehicle.ownership')
                    ->label('Ownership')
                    ->badge(),

                TextColumn::make('vehicle.region.name')
                    ->label('Region'),

                TextColumn::make('driver.name')
                    ->label('Driver'),

                TextColumn::make('driver.phone')
                    ->label('Driver Phone'),

                TextColumn::make('requester.name')
                    ->label('Requested By'),

                TextColumn::make('destination')
                    ->label('Destination')
                    ->limit(25),

                TextColumn::make('purpose')
                    ->label('Purpose')
                    ->limit(40),

                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->dateTime('d M Y H:i'),

                TextColumn::make('end_date')
                    ->label('End Date')
                    ->dateTime('d M Y H:i'),

                TextColumn::make('duration')
                    ->label('Duration')
                    ->getStateUsing(function ($record) {

                        $start = Carbon::parse($record->start_date);
                        $end = Carbon::parse($record->end_date);

                        return $start->diffInHours($end) . ' hours';
                    }),

                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'approved',
                        'primary' => 'completed',
                    ])
                    ->label('Status'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y'),

            ])

            ->filters([

                SelectFilter::make('status')
                    ->options([
                        'approved' => 'Approved',
                        'completed' => 'Completed',
                    ]),

                Filter::make('period')
                    ->form([
                        DatePicker::make('from')->label('From Date'),
                        DatePicker::make('until')->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data) {

                        return $query
                            ->when(
                                $data['from'],
                                fn($query) => $query->whereDate('start_date', '>=', $data['from'])
                            )
                            ->when(
                                $data['until'],
                                fn($query) => $query->whereDate('end_date', '<=', $data['until'])
                            );
                    }),

            ])

            ->headerActions([

                Tables\Actions\Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new VehicleBookingReportExport(),
                            'vehicle-booking-report.xlsx'
                        );
                    }),

            ])

            ->bulkActions([

                Tables\Actions\BulkAction::make('export_selected')
                    ->label('Export Selected')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function ($records) {

                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new VehicleBookingReportExport($records),
                            'vehicle-booking-report-selected.xlsx'
                        );
                    })

            ])

            ->defaultSort('start_date', 'desc')

            ->recordUrl(null)

            ->actions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'vehicle',
                'vehicle.region',
                'driver',
                'requester'
            ])
            ->whereIn('status', ['approved', 'completed']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicleBookingReports::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}

<?php

namespace App\Filament\Resources\ApprovalResource\Pages;

use App\Filament\Resources\ApprovalResource;
use Filament\Infolists\Components\ImageEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Infolist;

class ViewApproval extends ViewRecord
{
    protected static string $resource = ApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                InfoSection::make('Approval Details')
                    ->icon('heroicon-m-chart-bar-square')
                    ->schema([

                        ImageEntry::make('booking.vehicle.image')
                            ->label('')
                            ->size(265),

                        TextEntry::make('vehicle')
                            ->label('Vehicle')
                            ->weight('bold')
                            ->size(800)
                            ->state(
                                fn($record) =>
                                optional($record->booking->vehicle)->plate_number . ' - ' . optional($record->booking->vehicle)->brand . ' ' . optional($record->booking->vehicle)->model
                            ),

                        ImageEntry::make('booking.driver.image')
                            ->label('')
                            ->circular()
                            ->size(265)
                            ->visible(fn($record) => optional($record->booking)->driver !== null),

                        TextEntry::make('driver')
                            ->label('Driver')
                            ->state(fn($record) => $record)
                            ->visible(fn($record) => optional($record->booking)->driver !== null)
                            ->formatStateUsing(function ($record) {

                                $driver = $record->booking?->driver;

                                if (!$driver) return '-';

                                return "
            <div>
                <div class='font-bold'>
                    {$driver->name}
                </div>
                <div class='text-sm'>
                    Phone number: {$driver->phone}
                </div>
                <div class='text-sm'>
                    License number: {$driver->license_number}
                </div>
            </div>
        ";
                            })
                            ->html(),

                    ])
                    ->columns(2),

                InfoSection::make('Trip Information')
                    ->icon('heroicon-m-map-pin')
                    ->schema([

                        TextEntry::make('booking.destination')
                            ->label('Destination')
                            ->badge()
                            ->icon('heroicon-m-map-pin')
                            ->weight('bold'),

                        TextEntry::make('booking.purpose')
                            ->label('Purpose')
                            ->badge()
                            ->color('gray')
                            ->columnSpanFull(),

                    ])
                    ->columns(2),

                InfoSection::make('Schedule')
                    ->icon('heroicon-m-calendar-date-range')
                    ->schema([

                        TextEntry::make('booking.start_date')
                            ->icon('heroicon-m-calendar-date-range')
                            ->label('Start')
                            ->badge()
                            ->dateTime('d M Y H:i'),

                        TextEntry::make('booking.end_date')
                            ->icon('heroicon-m-calendar-date-range')
                            ->label('End')
                            ->badge()
                            ->dateTime('d M Y H:i'),

                    ])
                    ->columns(2),

                InfoSection::make('Approval Information')
                    ->icon('heroicon-m-cube-transparent')
                    ->schema([

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn($state) => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'completed' => 'primary',
                                'rejected' => 'danger',
                            })
                            ->formatStateUsing(fn($state) => ucfirst($state)),

                        TextEntry::make('level')
                            ->label('Level')
                            ->badge()
                            ->color(fn($state) => $state == 1 ? 'info' : 'warning'),

                        TextEntry::make('notes')
                            ->color('gray')
                            ->label('Notes')
                            ->badge()
                            ->default('-'),

                    ])
                    ->columns(2),

            ]);
    }
}

<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Infolists\Components\ImageEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Infolist;

class ViewBooking extends ViewRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

     public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                InfoSection::make('Booking Overview')
                    ->icon('heroicon-m-truck')
                    ->schema([

                        ImageEntry::make('vehicle.image')
                            ->label('')
                            ->size(265),

                        TextEntry::make('vehicle')
                            ->label('Vehicle')
                            ->weight('bold')
                            ->size(800)
                            ->state(
                                fn($record) =>
                                optional($record->vehicle)->plate_number . ' - ' . optional($record->vehicle)->brand . ' ' . optional($record->vehicle)->model
                            ),

                        ImageEntry::make('driver.image')
                            ->circular()
                            ->label('')
                            ->visible(fn($record) => $record->driver)
                            ->size(265),

                        TextEntry::make('driver')
                            ->label('Driver')
                            ->visible(fn ($record) => $record->driver)
                            ->formatStateUsing(function ($record) {
                                $driver = $record->driver;

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

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn($state) => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'completed' => 'primary',
                                'rejected' => 'danger',
                                'canceled' => 'danger',
                            })
                            ->formatStateUsing(fn($state) => ucfirst($state))
                            ->icons([
                                'heroicon-m-clock' => 'pending',
                                'heroicon-m-check-circle' => 'approved',
                                'heroicon-m-x-circle' => 'rejected',
                                'heroicon-m-check-badge' => 'completed'
                            ])
                            ->columnSpanFull()
                            ->inlineLabel()
                            ->alignCenter()
                            ->extraAttributes([
                                'class' => 'text-center'
                            ]),

                    ])
                    ->columns(2),

                // 📍 TRIP INFO
                InfoSection::make('Trip Information')
                    ->icon('heroicon-m-map-pin')
                    ->schema([

                        TextEntry::make('destination')
                            ->label('Destination')
                            ->badge()
                            ->icon('heroicon-m-map-pin')
                            ->weight('bold'),

                        TextEntry::make('purpose')
                            ->label('Purpose')
                            ->badge()
                            ->color('gray')
                            ->columnSpanFull(),

                    ])
                    ->columns(2),

                // 📅 SCHEDULE
                InfoSection::make('Schedule')
                    ->icon('heroicon-m-calendar-date-range')
                    ->schema([

                        TextEntry::make('start_date')
                            ->icon('heroicon-m-calendar-date-range')
                            ->label('Start')
                            ->badge()
                            ->dateTime('d M Y H:i'),

                        TextEntry::make('end_date')
                            ->icon('heroicon-m-calendar-date-range')
                            ->label('End')
                            ->badge()
                            ->dateTime('d M Y H:i'),

                    ])
                    ->columns(2),

                // 👤 REQUEST INFO
                InfoSection::make('Request Information')
                    ->icon('heroicon-m-user')
                    ->schema([

                        TextEntry::make('requester.name')
                            ->label('Requested By')
                            ->badge()
                            ->icon('heroicon-m-user'),

                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->icon('heroicon-m-calendar-date-range')
                            ->badge()
                            ->dateTime('d M Y H:i'),

                    ])
                    ->columns(2),

                // 🔥 APPROVAL FLOW (ini yang bikin beda)
                InfoSection::make('Approval Flow')
                    ->icon('heroicon-m-check-badge')
                    ->schema([

                        // LEVEL 1
                        TextEntry::make('approval_1')
                            ->label('Approver Level 1')
                            ->badge()
                            ->color(
                                fn($record) =>
                                optional($record->approvals->where('level', 1)->first())->status === 'approved'
                                    ? 'success'
                                    : (optional($record->approvals->where('level', 1)->first())->status === 'rejected'
                                        ? 'danger'
                                        : 'warning')
                            )
                            ->state(
                                fn($record) =>
                                optional($record->approvals->where('level', 1)->first())->notes
                                    ? optional($record->approvals->where('level', 1)->first())->notes
                                    : 'No notes'
                            ),

                        // LEVEL 2
                        TextEntry::make('approval_2')
                            ->label('Approver Level 2')
                            ->badge()
                            ->color(
                                fn($record) =>
                                optional($record->approvals->where('level', 2)->first())->status === 'approved'
                                    ? 'success'
                                    : (optional($record->approvals->where('level', 2)->first())->status === 'rejected'
                                        ? 'danger'
                                        : 'warning')
                            )
                            ->state(
                                fn($record) =>
                                optional($record->approvals->where('level', 2)->first())->notes
                                    ? optional($record->approvals->where('level', 2)->first())->notes
                                    : 'No notes'
                            ),

                    ])
                    ->columns(2),

            ]);
    }
}

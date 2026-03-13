<?php

namespace App\Filament\Resources\ApprovalResource\Pages;

use App\Filament\Resources\ApprovalResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;

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

                TextEntry::make('booking.vehicle.plate_number')
                    ->label('Vehicle'),

                TextEntry::make('booking.driver.name')
                    ->label('Driver'),

                TextEntry::make('booking.destination')
                    ->label('Destination'),

                TextEntry::make('booking.purpose')
                    ->label('Purpose'),

                TextEntry::make('booking.start_date')
                    ->label('Start Date')
                    ->dateTime(),

                TextEntry::make('booking.end_date')
                    ->label('End Date')
                    ->dateTime(),

                TextEntry::make('status')
                    ->badge(),

                TextEntry::make('notes')
                    ->label('Notes'),

            ]);
    }
}
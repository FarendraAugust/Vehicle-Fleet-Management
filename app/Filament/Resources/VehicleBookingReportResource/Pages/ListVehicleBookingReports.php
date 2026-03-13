<?php

namespace App\Filament\Resources\VehicleBookingReportResource\Pages;

use App\Filament\Resources\VehicleBookingReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVehicleBookingReports extends ListRecords
{
    protected static string $resource = VehicleBookingReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

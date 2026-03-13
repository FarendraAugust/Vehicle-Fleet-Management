<?php

namespace App\Filament\Resources\VehicleBookingReportResource\Pages;

use App\Filament\Resources\VehicleBookingReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVehicleBookingReport extends EditRecord
{
    protected static string $resource = VehicleBookingReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

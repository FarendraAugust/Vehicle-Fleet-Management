<?php

namespace App\Filament\Resources\VehicleUsageResource\Pages;

use App\Filament\Resources\VehicleUsageResource;
use App\Filament\Resources\VehicleUsageResource\Widgets\VehicleUsageStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVehicleUsages extends ListRecords
{
    protected static string $resource = VehicleUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            VehicleUsageStats::class,
        ];
    }
}

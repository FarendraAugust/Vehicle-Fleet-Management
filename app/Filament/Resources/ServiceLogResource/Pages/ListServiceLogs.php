<?php

namespace App\Filament\Resources\ServiceLogResource\Pages;

use App\Filament\Resources\ServiceLogResource;
use App\Filament\Resources\ServiceLogResource\Widgets\ServiceLogStats;
use App\Filament\Widgets\DashboardFilter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServiceLogs extends ListRecords
{
    protected static string $resource = ServiceLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DashboardFilter::class,
            ServiceLogStats::class
        ];
    }
}

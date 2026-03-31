<?php

namespace App\Filament\Resources\ApprovalResource\Pages;

use App\Filament\Resources\ApprovalResource;
use App\Filament\Resources\ApprovalResource\Widgets\ApprovalStats;
use App\Filament\Widgets\DashboardFilter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApprovals extends ListRecords
{
    protected static string $resource = ApprovalResource::class;

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
            ApprovalStats::class,
        ];
    }
}

<?php

namespace App\Filament\Resources\ServiceLogResource\Pages;

use App\Filament\Resources\ServiceLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServiceLog extends EditRecord
{
    protected static string $resource = ServiceLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

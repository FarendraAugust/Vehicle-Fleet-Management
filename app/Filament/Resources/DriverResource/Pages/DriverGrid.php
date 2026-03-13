<?php

namespace App\Filament\Resources\DriverResource\Pages;

use Filament\Resources\Pages\Page;
use App\Filament\Resources\DriverResource;
use App\Models\Driver;

class DriverGrid extends Page
{
    protected static string $resource = DriverResource::class;

    protected static string $view = 'filament.resources.driver-resource.pages.driver-grid';

    public $search = '';
    public $sort = 'latest';

    public function getDrivers()
    {
        return Driver::query()

            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })

            ->when($this->sort === 'name', fn ($q) => $q->orderBy('name'))

            ->when($this->sort === 'latest', fn ($q) => $q->latest())

            ->get();
    }
}
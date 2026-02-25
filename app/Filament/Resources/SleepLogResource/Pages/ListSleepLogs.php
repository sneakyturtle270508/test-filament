<?php

namespace App\Filament\Resources\SleepLogResource\Pages;

use App\Filament\Resources\SleepLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSleepLogs extends ListRecords
{
    protected static string $resource = SleepLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

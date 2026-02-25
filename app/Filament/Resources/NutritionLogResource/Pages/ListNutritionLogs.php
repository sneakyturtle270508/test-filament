<?php

namespace App\Filament\Resources\NutritionLogResource\Pages;

use App\Filament\Resources\NutritionLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNutritionLogs extends ListRecords
{
    protected static string $resource = NutritionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

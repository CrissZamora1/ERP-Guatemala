<?php

namespace App\Filament\Resources\PlanillaPeriodoResource\Pages;

use App\Filament\Resources\PlanillaPeriodoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlanillaPeriodos extends ListRecords
{
    protected static string $resource = PlanillaPeriodoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

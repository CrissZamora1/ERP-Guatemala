<?php

namespace App\Filament\Resources\PlanillaPeriodoResource\Pages;

use App\Filament\Resources\PlanillaPeriodoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlanillaPeriodo extends EditRecord
{
    protected static string $resource = PlanillaPeriodoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

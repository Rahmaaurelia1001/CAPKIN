<?php

namespace App\Filament\Resources\TargetIkks\Pages;

use App\Filament\Resources\TargetIkks\TargetIkkResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTargetIkk extends EditRecord
{
    protected static string $resource = TargetIkkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

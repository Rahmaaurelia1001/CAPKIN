<?php

namespace App\Filament\Resources\TargetPenarikanDanaResource\Pages;

use App\Filament\Resources\TargetPenarikanDanaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTargetPenarikanDana extends EditRecord
{
    protected static string $resource = TargetPenarikanDanaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\TargetPenarikanDanaResource\Pages;

use App\Filament\Resources\TargetPenarikanDanaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTargetPenarikanDana extends CreateRecord
{
    protected static string $resource = TargetPenarikanDanaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}

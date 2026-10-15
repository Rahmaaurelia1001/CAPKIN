<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (in_array($data['role'] ?? null, ['admin', 'kapus', 'kabag_umum'])) {
            $data['unit_id'] = null;
        }

        return $data;
    }
}

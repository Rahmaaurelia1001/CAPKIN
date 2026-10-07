<?php

namespace App\Filament\Resources\Ikks\Pages;

use App\Filament\Resources\Ikks\IkkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIkks extends ListRecords
{
    protected static string $resource = IkkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\TargetIkks\Pages;

use App\Filament\Resources\TargetIkks\TargetIkkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTargetIkks extends ListRecords
{
    protected static string $resource = TargetIkkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

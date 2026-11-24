<?php

namespace App\Filament\Resources\TargetPenarikanDanaResource\Pages;

use App\Filament\Resources\TargetPenarikanDanaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTargetPenarikanDanas extends ListRecords
{
    protected static string $resource = TargetPenarikanDanaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

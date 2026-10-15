<?php

namespace App\Filament\Resources\PaguAnggarans\Pages;

use App\Filament\Resources\PaguAnggarans\PaguAnggaranResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPaguAnggarans extends ListRecords
{
    protected static string $resource = PaguAnggaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

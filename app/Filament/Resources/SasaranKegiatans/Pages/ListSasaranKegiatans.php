<?php

namespace App\Filament\Resources\SasaranKegiatans\Pages;

use App\Filament\Resources\SasaranKegiatans\SasaranKegiatanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSasaranKegiatans extends ListRecords
{
    protected static string $resource = SasaranKegiatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

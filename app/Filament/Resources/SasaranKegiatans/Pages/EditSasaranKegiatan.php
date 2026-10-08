<?php

namespace App\Filament\Resources\SasaranKegiatans\Pages;

use App\Filament\Resources\SasaranKegiatans\SasaranKegiatanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSasaranKegiatan extends EditRecord
{
    protected static string $resource = SasaranKegiatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

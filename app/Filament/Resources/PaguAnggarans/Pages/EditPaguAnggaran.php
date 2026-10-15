<?php

namespace App\Filament\Resources\PaguAnggarans\Pages;

use App\Filament\Resources\PaguAnggarans\PaguAnggaranResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPaguAnggaran extends EditRecord
{
    protected static string $resource = PaguAnggaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

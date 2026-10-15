<?php

namespace App\Filament\Resources\AnggaranBulanans\Pages;

use App\Filament\Resources\AnggaranBulanans\AnggaranBulananResource;
use Filament\Resources\Pages\EditRecord;

class EditAnggaranBulanan extends EditRecord
{
    protected static string $resource = AnggaranBulananResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

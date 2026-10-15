<?php

namespace App\Filament\Resources\AnggaranBulanans\Pages;

use App\Filament\Resources\AnggaranBulanans\AnggaranBulananResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAnggaranBulanans extends ListRecords
{
    protected static string $resource = AnggaranBulananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

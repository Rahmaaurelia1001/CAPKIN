<?php

namespace App\Filament\Resources\Ikks\Pages;

use App\Filament\Resources\Ikks\IkkResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIkk extends CreateRecord
{
    protected static string $resource = IkkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('kembali')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(IkkResource::getUrl('index')),
        ];
    }
}

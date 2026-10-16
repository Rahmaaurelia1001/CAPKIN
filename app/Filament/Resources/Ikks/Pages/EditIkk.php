<?php

namespace App\Filament\Resources\Ikks\Pages;

use App\Filament\Resources\Ikks\IkkResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditIkk extends EditRecord
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
            DeleteAction::make(),
        ];
    }
}

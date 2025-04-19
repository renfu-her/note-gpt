<?php

namespace App\Filament\Resources\NoteFolderResource\Pages;

use App\Filament\Resources\NoteFolderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNoteFolder extends EditRecord
{
    protected static string $resource = NoteFolderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

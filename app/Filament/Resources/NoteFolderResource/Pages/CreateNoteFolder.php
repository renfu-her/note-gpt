<?php

namespace App\Filament\Resources\NoteFolderResource\Pages;

use App\Filament\Resources\NoteFolderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateNoteFolder extends CreateRecord
{
    protected static string $resource = NoteFolderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

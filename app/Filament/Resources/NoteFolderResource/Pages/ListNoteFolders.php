<?php

namespace App\Filament\Resources\NoteFolderResource\Pages;

use App\Filament\Resources\NoteFolderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNoteFolders extends ListRecords
{
    protected static string $resource = NoteFolderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

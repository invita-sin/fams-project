<?php

namespace App\Filament\Resources\FieldDocumentations\Pages;

use App\Filament\Resources\FieldDocumentations\FieldDocumentationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageFieldDocumentations extends ManageRecords
{
    protected static string $resource = FieldDocumentationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

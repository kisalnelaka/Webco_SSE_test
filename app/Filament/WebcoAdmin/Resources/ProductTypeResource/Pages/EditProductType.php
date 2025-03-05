<?php

namespace App\Filament\WebcoAdmin\Resources\ProductTypeResource\Pages;

use App\Filament\WebcoAdmin\Resources\ProductTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductType extends EditRecord
{
    protected static string $resource = ProductTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

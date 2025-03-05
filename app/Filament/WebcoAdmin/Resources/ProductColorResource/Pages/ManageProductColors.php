<?php

namespace App\Filament\WebcoAdmin\Resources\ProductColorResource\Pages;

use App\Filament\WebcoAdmin\Resources\ProductColorResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProductColors extends ManageRecords
{
    protected static string $resource = ProductColorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

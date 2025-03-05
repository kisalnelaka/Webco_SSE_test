<?php

namespace App\Filament\WebcoAdmin\Resources\ProductTypeResource\Pages;

use App\Filament\WebcoAdmin\Resources\ProductTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProductType extends CreateRecord
{
    protected static string $resource = ProductTypeResource::class;
}

<?php

namespace App\Filament\WebcoAdmin\Resources\ProductResource\Pages;

use App\Filament\WebcoAdmin\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
}

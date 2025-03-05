<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductColorResource\Pages;
use App\Filament\Resources\ProductColorResource\RelationManagers;
use App\Models\ProductColor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class ProductColorResource extends Resource
{
    protected static ?string $model = ProductColor::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required(),
            TextInput::make('hex_code')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('name'),
            TextColumn::make('hex_code'),
        ])
        ->filters([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProductColors::route('/'),
        ];
    }
}

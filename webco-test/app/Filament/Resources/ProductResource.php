<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use App\Models\ProductCategory;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;


class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            TextInput::make('name')->required(),
            Textarea::make('description'),
            Select::make('product_color_id')
                ->relationship('productColor', 'name')
                ->required(),
            Select::make('product_types')
                ->multiple()
                ->relationship('productTypes', 'name')
                ->reactive(),
            Select::make('product_categories')
                ->multiple()
                ->relationship('productCategories', 'name')
                ->options(function (callable $get) {
                    $typeIds = $get('product_types') ?? [];
                    if (empty($typeIds)) return [];
                    return ProductCategory::whereHas('productTypes', function ($query) use ($typeIds) {
                        $query->whereIn('product_types.id', $typeIds);
                    })->pluck('name', 'id');
                }),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
    public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            TextEntry::make('name'),
            TextEntry::make('description'),
            TextEntry::make('productColor.name'),
        ]);
}
}

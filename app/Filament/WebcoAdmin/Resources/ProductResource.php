<?php

namespace App\Filament\WebcoAdmin\Resources;

use App\Filament\WebcoAdmin\Resources\ProductResource\Pages;
use App\Filament\WebcoAdmin\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\Textarea::make('description'),
                Forms\Components\Select::make('product_color_id')
                    ->relationship('color', 'name')
                    ->required(),
                Forms\Components\Select::make('product_type_ids')
                    ->multiple()
                    ->relationship('productTypes', 'name')
                    ->reactive(),
                Forms\Components\Select::make('product_category_ids')
                    ->multiple()
                    ->relationship('categories', 'name')
                    ->options(function (callable $get) {
                        $typeIds = $get('product_type_ids') ?? [];
                        return ProductCategory::whereHas('productTypes', function ($query) use ($typeIds) {
                            $query->whereIn('product_types.id', $typeIds);
                        })->pluck('name', 'id');
                    })
                    ->disabled(fn (callable $get) => empty($get('product_type_ids'))),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('color.name'),
                Tables\Columns\TextColumn::make('productTypes.name')->listWithLineBreaks(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'view' => Pages\ViewProduct::route('/{record}'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Infolists\Components\TextEntry::make('name'),
            Infolists\Components\TextEntry::make('description'),
            Infolists\Components\TextEntry::make('color.name'),
            Infolists\Components\TextEntry::make('productTypes.name')->listWithLineBreaks(),
        ]);
}
}
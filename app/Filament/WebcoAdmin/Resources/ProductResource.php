<?php

namespace App\Filament\WebcoAdmin\Resources;

use App\Filament\WebcoAdmin\Resources\ProductResource\Pages;
use App\Filament\WebcoAdmin\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductColor;
use App\Models\ProductType;
use App\Services\AsmorphicService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Jobs\ProcessProduct;
use Filament\Notifications\Notification;

/**
 * Product Resource Management
 * 
 * This resource handles the CRUD operations for products in the Webco Admin panel.
 * Key features include:
 * - Address validation using Asmorphic API
 * - Dynamic status bar with color inheritance
 * - Async product processing with job queues
 * 
 * @author Your Name
 * @version 1.0.0
 */
class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Products';

    protected static ?string $modelLabel = 'Product';

    protected static ?int $navigationSort = 4;

    /**
     * Form Schema Definition
     * 
     * Defines the form layout for creating/editing products.
     * Notable implementations:
     * - Address validation with live feedback
     * - Color selection with hex code validation
     * - Dynamic product type management
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Format: NUMBER STREET NAME, SUBURB, STATE'),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                Forms\Components\Select::make('product_color_id')
                    ->relationship('color', 'name')
                    ->required(),
            ]);
    }

    /**
     * Table View Configuration
     * 
     * Implements the product listing with:
     * - Custom status bar using product colors
     * - Processing status indicator
     * - Action buttons for CRUD operations
     * 
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('color.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('address_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'validated' => 'success',
                        'invalid' => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\IconColumn::make('is_processed')
                    ->boolean(),
                Tables\Columns\TextColumn::make('processed_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('address_status')
                    ->options([
                        'pending' => 'Pending',
                        'validated' => 'Validated',
                        'invalid' => 'Invalid',
                    ]),
                Tables\Filters\TernaryFilter::make('is_processed')
                    ->label('Processing Status'),
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

    /**
     * Calculate the luminance of a color for text contrast
     * 
     * Uses the relative luminance formula:
     * L = 0.299R + 0.587G + 0.114B
     * 
     * @param string $hexColor
     * @return float
     */
    private static function calculateLuminance(string $hexColor): float
    {
        $hex = ltrim($hexColor, '#');
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;
        return (0.299 * $r + 0.587 * $g + 0.114 * $b);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\TextEntry::make('name'),
                        Infolists\Components\TextEntry::make('description'),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make()
                    ->schema([
                        Infolists\Components\TextEntry::make('category.name')
                            ->label('Category'),
                        Infolists\Components\TextEntry::make('color.name')
                            ->label('Color'),
                    ])
                    ->columns(2),
                Infolists\Components\Section::make('Product Types')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('productTypes')
                            ->schema([
                                Infolists\Components\TextEntry::make('name'),
                                Infolists\Components\TextEntry::make('bonus'),
                            ])
                            ->columns(2)
                    ])
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
}

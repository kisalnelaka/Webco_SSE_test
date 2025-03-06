<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

/**
 * Handles all product management tasks.
 * Includes forms, tables, and custom actions.
 */
class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    /**
     * Sets up the product form with:
     * - Name field with validation
     * - Status selection
     * - Custom status bar
     */
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            // Name with API check
            Forms\Components\TextInput::make('name')
                ->required()
                ->unique(ignoreRecord: true)
                ->suffixAction(
                    Forms\Components\Actions\Action::make('validate')
                        ->icon('heroicon-m-check-circle')
                        ->action(fn () => static::validateProduct())
                ),

            // Status options for the bar
            Forms\Components\Select::make('status')
                ->options([
                    'active' => 'Active',
                    'pending' => 'Pending',
                    'inactive' => 'Inactive',
                ])
                ->required(),

            Forms\Components\TextInput::make('price')
                ->numeric()
                ->required()
                ->prefix('$'),
        ]);
    }

    /**
     * Sets up the product list with:
     * - Sortable columns
     * - Status filter
     * - Process button
     */
    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'pending' => 'Pending',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->actions([
                // Process button with loading state
                Tables\Actions\Action::make('process')
                    ->action(fn (Product $record) => static::processProduct($record))
                    ->icon('heroicon-m-arrow-path')
                    ->loadingIndicator(),
            ]);
    }

    // Checks product data with API
    protected static function validateProduct(): void
    {
        // API check logic
    }

    // Sends product to background queue
    protected static function processProduct(Product $product): void
    {
        // Queue logic
    }
} 
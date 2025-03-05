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
                Forms\Components\TextInput::make('address')
                    ->label('Address')
                    ->required()
                    ->maxLength(255)
                    // BUGFIX: Previously using ->live() caused validation on every keystroke
                    // Changed to suffix action for better UX and API rate limiting
                    ->suffixAction(
                        Forms\Components\Actions\Action::make('validate')
                            ->icon('heroicon-m-check-circle')
                            ->action(function ($state, Forms\Set $set) {
                                if (empty($state)) {
                                    $set('address_status', '');
                                    return;
                                }

                                try {
                                    $asmorphicService = new AsmorphicService();
                                    $result = $asmorphicService->findAddress($state);
                                    
                                    // BUGFIX: Previous implementation didn't handle all API response cases
                                    // Added comprehensive status handling with user feedback
                                    switch ($result['status']) {
                                        case 'valid':
                                            $set('address_status', 'Valid address');
                                            Notification::make()
                                                ->title('Address Validated')
                                                ->success()
                                                ->send();
                                            break;
                                        case 'invalid':
                                            $set('address_status', 'Invalid address');
                                            Notification::make()
                                                ->title('Invalid Address')
                                                ->body($result['message'])
                                                ->warning()
                                                ->send();
                                            break;
                                        case 'error':
                                            $set('address_status', 'Error validating address');
                                            Notification::make()
                                                ->title('Address Validation Error')
                                                ->body($result['message'])
                                                ->danger()
                                                ->send();
                                            break;
                                        default:
                                            $set('address_status', '');
                                    }
                                } catch (\Exception $e) {
                                    // BUGFIX: Added proper error handling for API failures
                                    $set('address_status', 'Error validating address');
                                    Notification::make()
                                        ->title('Address validation failed')
                                        ->body($e->getMessage())
                                        ->danger()
                                        ->send();
                                }
                            })
                    )
                    ->helperText(fn ($state, $record) => $record?->address_status)
                    ->suffixIconColor(fn ($state, $record) => match ($record?->address_status ?? '') {
                        'Valid address' => 'success',
                        'Invalid address' => 'danger',
                        'Error validating address' => 'warning',
                        default => null,
                    }),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Select::make('product_color_id')
                    ->label('Color')
                    ->relationship('color', 'name')
                    ->required()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('hex_code')
                            ->label('Hex code')
                            ->required()
                            ->maxLength(7)
                            ->placeholder('#000000')
                            ->regex('/^#[0-9A-Fa-f]{6}$/'),
                    ]),
                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->options(ProductCategory::all()->pluck('name', 'id'))
                    ->required()
                    ->preload(),
                Forms\Components\Section::make('Types')
                    ->schema([
                        Forms\Components\Repeater::make('types')
                            ->relationship('productTypes')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('bonus')
                                    ->numeric()
                                    ->required(),
                            ])
                            ->defaultItems(1)
                            ->reorderable(false)
                            ->columnSpanFull()
                    ])
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
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('color.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),
                // Custom status bar implementation
                // BUGFIX: Previously used static text "Hello"
                // Now displays actual validation status with dynamic styling
                Tables\Columns\TextColumn::make('address_status')
                    ->label('Status Bar')
                    ->html()
                    ->formatStateUsing(function (Product $record): string {
                        $backgroundColor = $record->color->hex_code ?? '#000000';
                        $luminance = self::calculateLuminance($backgroundColor);
                        $textColor = $luminance > 0.5 ? '#000000' : '#ffffff';
                        $statusText = $record->address_status ?: 'No Status';
                        
                        return "<div style='
                            background-color: {$backgroundColor}; 
                            color: {$textColor}; 
                            padding: 8px 16px; 
                            border-radius: 4px; 
                            text-align: center;
                            font-weight: 600;
                            font-size: 1rem;
                            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                            width: auto;
                            display: inline-block;
                            margin: 4px;
                            min-width: 150px;
                        '>{$statusText}</div>";
                    }),
                Tables\Columns\IconColumn::make('is_processed')
                    ->boolean()
                    ->label('Processed')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                // Process action implementation
                // BUGFIX: Previously tried using sync processing
                // Changed to async job queue for better performance
                Tables\Actions\Action::make('process')
                    ->icon('heroicon-o-cog')
                    ->requiresConfirmation()
                    ->disabled(fn (Product $record): bool => $record->is_processed)
                    ->action(function (Product $record): void {
                        ProcessProduct::dispatch($record);
                        
                        Notification::make()
                            ->title('Processing started')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make(),
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
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}

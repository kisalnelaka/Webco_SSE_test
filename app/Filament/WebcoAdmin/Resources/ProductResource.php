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

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Products';

    protected static ?string $modelLabel = 'Product';

    protected static ?int $navigationSort = 4;

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
                                    
                                    // Set the status message based on the validation result
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('color.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),
                Tables\Columns\TextColumn::make('address_status')
                    ->label('Status Bar')
                    ->html()
                    ->formatStateUsing(function (Product $record): string {
                        $backgroundColor = $record->color->hex_code ?? '#000000';
                        
                        // Convert hex to RGB to determine text color
                        $hex = ltrim($backgroundColor, '#');
                        $r = hexdec(substr($hex, 0, 2));
                        $g = hexdec(substr($hex, 2, 2));
                        $b = hexdec(substr($hex, 4, 2));
                        
                        // Calculate relative luminance
                        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
                        
                        // Use white text for dark backgrounds, black for light backgrounds
                        $textColor = $luminance > 0.5 ? '#000000' : '#ffffff';
                        
                        // Get the status text with a default value
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
                    })
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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

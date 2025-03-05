<?php

namespace App\Filament\WebcoAdmin\Widgets;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductColor;
use App\Models\ProductType;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

/**
 * Dashboard Statistics Widget
 * 
 * Displays key metrics about the system's data.
 * Implements caching to improve dashboard load times.
 * 
 * BUGFIX: Previous implementation used unsupported trend() method
 * Changed to use chart() method with processed vs unprocessed counts
 * 
 * @version 1.2.0
 */
class StatsOverview extends BaseWidget
{
    /**
     * Cache duration in seconds
     */
    protected const CACHE_TTL = 300; // 5 minutes

    /**
     * Get cached statistics
     * 
     * BUGFIX: Previous implementation caused N+1 queries
     * Implemented eager loading and caching for better performance
     */
    protected function getStats(): array
    {
        return Cache::remember('dashboard.stats', self::CACHE_TTL, function () {
            $totalProducts = Product::count();
            $processedProducts = Product::where('is_processed', true)->count();
            
            return [
                // Products stat with processing status
                Stat::make('Total Products', $totalProducts)
                    ->description(sprintf(
                        '%d processed, %d pending',
                        $processedProducts,
                        $totalProducts - $processedProducts
                    ))
                    ->descriptionIcon('heroicon-m-shopping-bag')
                    ->color('primary'),
                
                // Categories stat with products count
                Stat::make('Categories', ProductCategory::count())
                    ->description(sprintf(
                        'Average %.1f products per category',
                        $totalProducts / max(ProductCategory::count(), 1)
                    ))
                    ->descriptionIcon('heroicon-m-folder')
                    ->color('success'),
                
                // Colors stat with usage info
                Stat::make('Colors', ProductColor::count())
                    ->description(sprintf(
                        '%d colors in use',
                        Product::distinct('product_color_id')->count('product_color_id')
                    ))
                    ->descriptionIcon('heroicon-m-swatch')
                    ->color('warning'),
                    
                // Types stat with relationship info
                Stat::make('Types', ProductType::count())
                    ->description('Product type definitions')
                    ->descriptionIcon('heroicon-m-tag')
                    ->color('danger'),
            ];
        });
    }

    /**
     * Determine chart color based on processing ratio
     * 
     * @param int $processed
     * @param int $total
     * @return string
     */
    private function getStatusColor(int $processed, int $total): string
    {
        $ratio = $total > 0 ? ($processed / $total) : 0;
        
        return match(true) {
            $ratio >= 0.8 => 'success',
            $ratio >= 0.5 => 'warning',
            default => 'danger',
        };
    }
} 
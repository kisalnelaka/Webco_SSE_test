<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Shows product stats on the dashboard.
 * Counts products by status and total.
 */
class ProductStats extends BaseWidget
{
    // How often to update the numbers
    protected static ?string $pollingInterval = '15s';

    /**
     * Shows three stats:
     * - Active products
     * - Products in queue
     * - Total count
     */
    protected function getStats(): array
    {
        return [
            // Count of active ones
            Stat::make('Active Products', Product::where('status', 'active')->count())
                ->description('Products marked as active')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([3, 8, 12, 15, 20]),

            // Count of ones in queue
            Stat::make('In Processing', Product::where('is_processed', false)->count())
                ->description('In queue')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            // Total number
            Stat::make('Total Products', Product::count())
                ->description('All products')
                ->descriptionIcon('heroicon-m-shopping-bag'),
        ];
    }
} 
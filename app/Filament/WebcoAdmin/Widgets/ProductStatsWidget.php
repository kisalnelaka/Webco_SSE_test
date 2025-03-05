<?php

namespace App\Filament\WebcoAdmin\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Products', Product::count())
                ->description('Total number of products in the system')
                ->descriptionIcon('heroicon-m-cube')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),

            Stat::make('Products This Month', Product::whereMonth('created_at', now()->month)->count())
                ->description('Products created this month')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),

            Stat::make('Average Products Per Category', 
                number_format(Product::count() / max(1, \App\Models\ProductCategory::count()), 1))
                ->description('Products per category')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
        ];
    }
} 
<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Resources\Components\Tab;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('New Orders', Order::query()->where('status', 'new')->count()),
            Stat::make('Porcessing Orders', Order::query()->where('status', 'processing')->count()),
            // Stat::make('Shipped Orders', Order::query()->where('status', 'shipped')->count()),
            // Stat::make('Completed Orders', Order::query()->where('status', 'completed')->count()),
            Stat::make('Average Sales', Number::currency(Order::query()->avg('grand_total'), 'NGN')),
        ];
    }

   
}

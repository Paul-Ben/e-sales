<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrderStats::class
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('All'),
            1 => Tab::make('New')->query(fn () => Order::where('status', 'new')),
            2 => Tab::make('Pending')->query(fn () => Order::where('status', 'pending')),
            3 => Tab::make('Processing')->query(fn () => Order::where('status', 'processing')),
            4 => Tab::make('Shipped')->query(fn () => Order::where('status', 'shipped')),
            5 => Tab::make('Completed')->query(fn () => Order::where('status', 'completed')),

        ];
    }
}

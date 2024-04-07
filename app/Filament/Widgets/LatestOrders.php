<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions\Action as ActionsAction;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(OrderResource::getEloquentQuery())
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order ID'),
                TextColumn::make('User.name')
                    ->searchable(),
                TextColumn::make('grand_total')
                    ->money('NGN'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'info',
                        'pending' => 'warning',
                        'processing' => 'info',
                        'shipped' => 'info',
                        'completed' => 'success',
                        'delivered' => 'success',
                        'declined' => 'danger',
                        'cancled' => 'danger'
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'new' => 'heroicon-m-sparkles',
                        'pending' => 'heroicon-m-arrow-path',
                        'processing' => 'heroicon-m-arrow-path',
                        'shipped' => 'heroicon-m-truck',
                        'completed' => 'heroicon-m-check-badge',
                        'delivered' => 'heroicon-m-check-badge',
                        'declined' => 'heroicon-m-x-circle',
                        'cancled' => 'heroicon-m-x-circle'
                    })
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('payment_status')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('shipping_method'),
                TextColumn::make('created_at')
                    ->label('Order Date')
                    ->dateTime(),

            ])
            ->actions([
                Action::make('View Order')
                ->color('info')
                ->icon('heroicon-o-eye')
                ->url(fn (Order $record): string => OrderResource::getUrl('view', ['record' => $record])),
            ]);
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Order Information')->schema([
                        Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                        TextInput::make('grand_total')
                            ->numeric()
                            ->required(),
                        Select::make('payment_method')
                            ->options([
                                'Cash on delivery' => 'Cash on delivery',
                                'Card Payment' => 'Card Payment',
                                'Transfer' => 'Transfer',
                            ])
                            ->required()
                            ->searchable(),
                        Select::make('payment_status')
                            ->options([
                                'Pending' => 'Pending',
                                'Paid' => 'Paid',
                                'Failed' => 'Failed',
                                'Cancled' => 'Cancled',
                            ])
                            ->required()
                            ->searchable()
                            ->default('pending'),
                        Section::make('status')->schema([
                            ToggleButtons::make('status')
                                ->options([
                                    'new' => 'New',
                                    'pending' => 'Pending',
                                    'processing' => 'Processing',
                                    'shipped' => 'Shipped',
                                    'completed' => 'Completed',
                                    'delivered' => 'Delivered',
                                    'declined' => 'Declined',
                                    'cancled' => 'Cancled'
                                ])->default('Pending')
                                ->inline()
                                ->required()
                                ->colors([
                                    'new' => 'info',
                                    'pending' => 'warning',
                                    'processing' => 'info',
                                    'shipped' => 'info',
                                    'completed' => 'success',
                                    'delivered' => 'success',
                                    'declined' => 'danger',
                                    'cancled' => 'danger'
                                ])
                                ->icons([
                                    'new' => 'heroicon-m-sparkles',
                                    'pending' => 'heroicon-m-arrow-path',
                                    'processing' => 'heroicon-m-arrow-path',
                                    'shipped' => 'heroicon-m-truck',
                                    'completed' => 'heroicon-m-check-badge',
                                    'delivered' => 'heroicon-m-check-badge',
                                    // 'declined' => 'danger', 
                                    'cancled' => 'heroicon-m-x-circle'
                                ]),
                        ]),

                        Select::make('currency')
                            ->options([
                                'ngn' => 'NGN',
                                'usd' => 'USD',
                                'gbp' => 'GBP',
                                'inr' => 'INR'
                            ])->searchable()->live()->preload()->required(),

                        Select::make('shipping_method')
                            ->options([
                                'fedex' => 'FedEx',
                                'dhl' => 'DHL',
                                'bnl' => 'BNL',
                                'ptl' => 'PTL'
                            ])->searchable()->live()->preload(),
                        Textarea::make('notes')
                            ->columnSpanFull()
                    ])->columns(2),

                    Section::make('Order Items')->schema([
                        Repeater::make('order_items')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, Set $set) => $set('unit_price', optional(Product::find($state))
                                        ->price ?? 0))
                                    ->afterStateUpdated(fn ($state, Set $set) => $set('total_price', optional(Product::find($state))
                                        ->price ?? 0))
                                    ->columnSpan(4),

                                TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, Set $set, Get $get) => $set('total_price', $state * $get('unit_price')))
                                    ->columnSpan(2),
                                TextInput::make('unit_price')
                                    ->required()
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(3),
                                TextInput::make('total_price')
                                    ->numeric()
                                    ->required()
                                    ->dehydrated()
                                    ->columnSpan(3)
                            ])->columns(12),
                        Placeholder::make('grand_totoal_placeholder')
                            ->label('Grand Total')
                            ->content(function (Set $set, Get $get) {
                                $total = 0;
                                if (!$repeaters = $get('order_items')) {
                                    return $total;
                                }

                                foreach ($repeaters as $key => $repeater) {
                                    $total += $get("order_items.{$key}.total_price");
                                }
                                $set("grand_total", $total);
                                return  Number::currency($total, 'NGN');
                            }),

                        Hidden::make('grand_total')
                            ->default(0),
                    ])


                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->sortable(),
                Tables\Columns\TextColumn::make('grand_total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('currency')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('shipping_cost')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('shipping_method')
                    ->searchable(),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'new' => 'New',
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'completed' => 'Completed',
                        'delivered' => 'Delivered',
                        'declined' => 'Declined',
                        'cancled' => 'Cancled'
                    ])
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                ]),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}

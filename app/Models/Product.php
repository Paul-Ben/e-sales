<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\Cast;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
            'brand_id',
            'name',
            'slug',
            'description',
            'price',
            'images',
            'in_stock',
            'is_active',
            'is_featured',
            'is_onsale'
    ];

    protected $casts = [
        'images' => 'array'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }
}

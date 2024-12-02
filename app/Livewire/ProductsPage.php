<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Products')]
class ProductsPage extends Component
{
    use WithPagination;

    #[Url]
    public $selected_categories = [];

    #[Url]
    public $selected_brands = [];

    #[Url]
    public $in_stock;

    #[Url]
    public $is_onsale;

    #[Url]
    public $price_range = 0;

    #[Url]
    public $sort;

    public function render()
    {
        $products = Product::where('is_active', 1)->paginate(9);

        if (!empty($this->selected_categories)) {
            $products = Product::whereIn('category_id', $this->selected_categories)->paginate(9);
        }

        if (!empty($this->selected_brands)) {
            // $products = Product::whereIn('brand_id', $this->selected_brands)->whereIn('category_id', $this->selected_categories)->where('is_active', 1)->paginate(9);
            $products = Product::whereIn('brand_id', $this->selected_brands)->where('is_active', 1)->paginate(9);

        }

        if (!empty($this->selected_brands && $this->selected_categories)) {
            $products = Product::whereIn('brand_id', $this->selected_brands)->whereIn('category_id', $this->selected_categories)->where('is_active', 1)->paginate(9);
        }

        if ($this->in_stock) {
            $products = Product::where('is_active', 1)->where('in_stock', 1)->paginate(9);
        }

        if ($this->is_onsale) {
            $products = Product::where('is_onsale', 1)->where('is_active', 1)->paginate(9);
        }

        if ($this->price_range) {
            $products = Product::where('is_active', 1)->whereBetween('price', [0, $this->price_range])->paginate(9);
        }

        if ($this->sort == 'price') {
            $products = Product::where('is_active', 1)->orderBy('price')->paginate(9);
        }

        if ($this->sort == 'latest') {
            $products = Product::where('is_active', 1)->latest()->paginate(9);
        }

      
        $brands = Brand::where('is_active', 1)->get();
        $categories = Category::where('is_active', 1)->get();
        return view('livewire.products-page', compact('products', 'brands', 'categories'));
    }
}

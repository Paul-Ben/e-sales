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
    public $selected_categories= [];

    public function render()
    {
        $products = Product::where('is_active', 1)->paginate(9);
        $brands = Brand::where('is_active', 1)->get();
        $categories = Category::where('is_active', 1)->get();
        return view('livewire.products-page', compact('products', 'brands', 'categories'));
    }
}

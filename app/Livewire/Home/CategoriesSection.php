<?php

namespace App\Livewire\Home;

use App\Models\Category;
use Livewire\Component;

class CategoriesSection extends Component
{
    public function render()
    {
        $categories = Category::where('is_active', 1)->get();
        return view('livewire.home.categories-section', compact('categories'));
    }
}

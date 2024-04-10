<?php

namespace App\Livewire\Home;

use App\Models\Brand;
use Livewire\Component;

class BrandSection extends Component
{
    
    public function render()
    {
        $brands = Brand::where('is_active', 1)->get();
        return view('livewire.home.brand-section', compact('brands'));
    }
}

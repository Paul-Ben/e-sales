<?php

namespace App\Livewire;

use App\Models\Brand;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('EsalesMkd')]
class HomePage extends Component
{

    public function render()
    {
        return view('livewire.home-page');
    }
}

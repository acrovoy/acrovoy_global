<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CharCounter extends Component
{
    public $max;

    public function __construct($max = 120)
    {
        $this->max = $max;
    }

    public function render()
    {
        return view('components.char-counter');
    }
}
<?php

namespace App\View\Components\Supplier;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Tabs extends Component
{
    public $tabs;
public $supplier;

    public function __construct($tabs, $supplier)
    {
        $this->tabs = $tabs;
        $this->supplier = $supplier;
    }

    public function render()
    {
        return view('components.supplier.tabs');
    }
}
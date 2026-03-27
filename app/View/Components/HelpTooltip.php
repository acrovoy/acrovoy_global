<?php

namespace App\View\Components;

use Illuminate\View\Component;

class HelpTooltip extends Component
{
    public string $width;

    /**
     * Create a new component instance.
     *
     * @param string $width
     */
    public function __construct(string $width = 'w-64')
    {
        $this->width = $width;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.help-tooltip');
    }
}
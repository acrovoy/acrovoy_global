<?php

namespace App\View\Components;

use Illuminate\View\Component;

class LocationPicker extends Component
{
    /**
     * Optional: preselected location id
     */
    public ?int $value;

    /**
     * Input name for form binding
     */
    public string $name;
    public $countries;

    public function __construct(
        ?int $value = null,
        string $name = 'location_id',
        $countries = []
    ) {
        $this->value = $value;
        $this->name = $name;
        $this->countries = $countries;
    }

    

   

    public function render()
    {
        return view('components.location-picker');
    }
}
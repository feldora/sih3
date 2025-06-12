<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MapRoutePicker extends Component
{
    /**
     * The old coordinates.
     *
     * @var mixed
     */
    public $oldCoordinates;
    
    /**
     * Create a new component instance.
     */
    public function __construct($coordinates = null)
    {
        $this->oldCoordinates = $coordinates ? json_decode($coordinates) : null;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.map-route-picker');
    }
}

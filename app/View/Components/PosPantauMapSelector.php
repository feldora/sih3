<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PosPantauMapSelector extends Component
{
    public string $name;
    public ?string $value;
    public ?string $label;
    public bool $required;
    public ?string $placeholder;
    public string $mapHeight;
    public array $centerCoordinates;
    public int $defaultZoom;
    public bool $viewOnly;
    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name = 'pos_pantau_id',
        ?string $value = null,
        ?string $label = null,
        bool $required = false,
        ?string $placeholder = 'Pilih Pos Pantau dari Map',
        string $mapHeight = '400px',
        array $centerCoordinates = [-1.2, 121.2],
        int $defaultZoom = 10,
        bool $viewOnly = false,
    ) {
        $this->name = $name;
        $this->value = $value;
        $this->label = $label;
        $this->required = $required;
        $this->placeholder = $placeholder;
        $this->mapHeight = $mapHeight;
        $this->centerCoordinates = $centerCoordinates;
        $this->defaultZoom = $defaultZoom;
        $this->viewOnly = $viewOnly;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.pos-pantau-map-selector');
    }
}
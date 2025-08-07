<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SelectJenisPos extends Component
{
    public $name;
    public $selected;
    public $options;

    /**
     * @param string $name       = name atribut <select>
     * @param string|null $selected = nilai default
     */
    public function __construct(string $name = 'jenis_pos', string $selected = null)
    {
        $this->name = $name;
        $this->selected = $selected;

        $this->options = [
            'Pos Curah Hujan'     => 'Pos Curah Hujan',
            'Pos Duga Air'        => 'Pos Duga Air',
            'Pos Klimatologi'     => 'Pos Klimatologi',
        ];
    }

    public function render()
    {
        return view('components.select-jenis-pos');
    }
}

<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Instansi;

class InstansiSelect extends Component
{
    public $name;
    public $selected;
    public $instansis;

    public function __construct($name = 'instansi_id', $selected = null)
    {
        $this->name = $name;
        $this->selected = $selected;
        $this->instansis = Instansi::all();
    }

    public function render()
    {
        return view('components.instansi-select');
    }
}

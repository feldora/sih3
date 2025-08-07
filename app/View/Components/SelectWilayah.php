<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Kabupaten;

class SelectWilayah extends Component
{
    public $kabupatenId;
    public $kecamatanId;
    public $desaId;
    public $kabupatens;

    public function __construct($kabupatenId = null, $kecamatanId = null, $desaId = null)
    {
        $this->kabupatenId = $kabupatenId;
        $this->kecamatanId = $kecamatanId;
        $this->desaId = $desaId;
        $this->kabupatens = Kabupaten::all();
    }

    public function render()
    {
        return view('components.select-wilayah');
    }
}

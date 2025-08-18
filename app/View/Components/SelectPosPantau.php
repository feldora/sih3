<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use App\Models\PosPantau;

class SelectPosPantau extends Component
{
    public $name;
    public $id;
    public $selected;
    public $posPantau;

    /**
     * Create a new component instance.
     *
     * @param string $name
     * @param string|null $id
     * @param int|string|null $selected
     */
    public function __construct($name = 'pos_pantau_id', $id = null, $selected = null)
    {
        $this->name = $name;
        $this->id = $id ?? $name;
        $this->selected = $selected;

        // Ambil data pos pantau
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): \Illuminate\Contracts\View\View|Closure|string
    {
        $user = \Auth::user();

        if($user->is_admin) {
            $this->posPantau = PosPantau::orderBy('nama_pos')->get();
        } else {
            $instansi_id = $user->instansi_id ?? null;
            $this->posPantau = PosPantau::query()
                ->when($instansi_id, fn($q) => $q->where('instansi_id', $instansi_id))
                ->orderBy('nama_pos')
                ->get();
        }
        
        return view('components.select-pos-pantau');
    }
}

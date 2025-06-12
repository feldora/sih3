<?php
namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\WilayahSungai;

class WilayahSungaiComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $wilayahSungai = WilayahSungai::all();
        $view->with('wilayahSungai', $wilayahSungai);
    }
}
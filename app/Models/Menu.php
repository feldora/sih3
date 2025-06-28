<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['title', 'url', 'icon', 'menu_type', 'zona', 'parent_id', 'order', 'permission_name'];

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }
    
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function position($parent_id = null)
    {
        if ($parent_id) {
            $menus = Menu::where('parent_id', $parent_id)
                ->whereNot('title', 'pengaturan')
                ->orderBy('parent_id')
                ->orderBy('order')
                ->get();
        } else {
            $menus = Menu::whereNull('parent_id')
                ->whereNot('title', 'pengaturan')
                ->orderBy('zona')
                ->orderBy('order')
                ->get();
        }

        $order = [
            (object) ['value' => 1, 'text' => 'first'],
        ];
        foreach ($menus as $menu) {
            $key = $menu->order + 1;
            $new = new \stdClass();
            $new->value = $key;
            $new->text = "after {$menu->title} - {$menu->zona}";
            $order[] = $new;
        }

        return $order;
    }

}

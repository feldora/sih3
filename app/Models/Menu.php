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

}

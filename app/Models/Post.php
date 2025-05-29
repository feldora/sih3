<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'content', 'user_id', 'category_id', 'status'];

    // Relasi ke User (One-to-Many)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Category (Many-to-One)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke Tags (Many-to-Many)
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}

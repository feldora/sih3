<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class Post extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = ['title', 'slug', 'content', 'user_id', 'category_id', 'status', 'instansi_id', 'pos_pantau_id', 'views'];

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

    public function pos_pantau()
    {
        return $this->belongsTo(PosPantau::class);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }
}

use App\Models\Post;

public function boot()
{
    parent::boot();

    // Binding 'post' route param ke model Post berdasarkan slug
    Route::bind('post', function ($value) {
        return Post::where('slug', $value)->firstOrFail();
    });
}

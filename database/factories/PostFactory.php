<?php
namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $user = \App\Models\User::inRandomOrder()->first();
        $category = \App\Models\Category::inRandomOrder()->first();
        // $tags = \App\Models\Tag::inRandomOrder()->take(rand(1, 3))->pluck('id')->toArray();

        return [
            'title' => $this->faker->sentence,
            'slug' => Str::slug($this->faker->sentence),
            'content' => $this->faker->paragraph,
            'user_id' => $user ? $user->id : null,
            'category_id' => $category ? $category->name : null,
            'status' => $this->faker->randomElement(['draft', 'published']),
            'views' => $this->faker->numberBetween(0, 1000),
            // 'tags' => $tags,
        ];
    }
}
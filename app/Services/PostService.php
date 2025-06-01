<?php
namespace App\Services;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PostService
{
    public function store(array $data)
    {
        DB::beginTransaction();
        try {
            $category = Category::where('name', $data['category_id'])->firstOrFail();

            $postData = [
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'content' => $data['content'],
                'user_id' => $data['user_id'] ?? Auth::id(),
                'category_id' => $category->id,
                'status' => $data['status'],
                'views' => $data['views'] ?? 0,
            ];

            $post = Post::create($postData);

            if (!empty($data['tags'])) {
                $tags = collect($data['tags'])->flatten()->filter()->all();
                $post->tags()->sync($tags);
            }

            DB::commit();
            return $post;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(Post $post, array $data)
    {
        DB::beginTransaction();
        try {
            $category = Category::where('name', $data['category_id'])->firstOrFail();

            $postData = [
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'content' => $data['content'],
                'category_id' => $category->id,
                'status' => $data['status'],
            ];

            $post->update($postData);

            if (!empty($data['tags'])) {
                $tags = collect($data['tags'])->flatten()->filter()->all();
                $post->tags()->sync($tags);
            }

            DB::commit();
            return $post;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
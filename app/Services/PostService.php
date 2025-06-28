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
    public function getPosts(array $filters = [])
    {


        if (in_array('admin', Auth::user()->getRoleNames()->toArray())) {
            $query = Post::with(['user', 'category', 'tags']);
        } else {
            $query = Post::with(['user', 'category', 'tags'])
                ->whereIn('role', Auth::user()->getRoleNames()->toArray())
                ->orWhere('role', null);
        }

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Sorting
        $sortField = $filters['sort'] ?? 'created_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $filters['per_page'] ?? 10;
        return $query->paginate($perPage);
    }

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
                'role' => $data['role'] ?? null,
                'views' => $data['views'] ?? 0,
            ];

            $post = Post::create($postData);

            if (!empty($data['tags'])) {
                $tags = collect($data['tags'])->flatten()->filter()->all();
                $post->tags()->sync($tags);
            }

            if (!empty($data['featured_image'])) {
                $post->addMedia($data['featured_image'])
                    ->toMediaCollection('featured_image');
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
            
            if (!empty($data['featured_image'])) {
                $post->clearMediaCollection('featured_image');
                $post->addMedia($data['featured_image'])
                    ->toMediaCollection('featured_image');
            }

            DB::commit();
            return $post;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
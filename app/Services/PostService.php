<?php
namespace App\Services;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Services\MediaService;
use Illuminate\Http\UploadedFile;

class PostService
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

public function getPosts(array $filters = [])
{
    $user = Auth::user();

    if ($user && in_array('admin', $user->getRoleNames()->toArray())) {
        $query = Post::with(['user', 'category', 'tags', 'media']);
    } else if ($user) {
        $query = Post::with(['user', 'category', 'tags', 'media'])
            ->whereIn('role', $user->getRoleNames()->toArray())
            ->orWhere('role', null);
    } else {
        $query = Post::with(['user', 'category', 'tags', 'media']);
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
    if (!empty($filters['selected_categories_type'])) {
        $query->whereHas('category', function ($q) use ($filters) {
            $q->where('type', ($filters['selected_categories_type'] ?? 'post'));
        });
    }
    // Apply filter for tags
    if (!empty($filters['tags'])) {
        $tags = is_array($filters['tags']) ? $filters['tags'] : [$filters['tags']];
        $query->whereHas('tags', function ($q) use ($tags) {
            $q->whereIn('name', $tags);
        });
    }

    // Sorting
    $sortField = $filters['sort'] ?? 'created_at';
    $sortDirection = $filters['direction'] ?? 'desc';
    $query->orderBy($sortField, $sortDirection);

    // Pagination
    $perPage = $filters['per_page'] ?? 10;
    return $query->paginate($perPage);
}

public function getPublicPosts($category = null, array $filters = [])
{
    $query = Post::where('status', 'published')->with('user', 'category', 'tags');
    if ($category) {
        $query->whereHas('category', function ($q) use ($category) {
            $q->where('name', $category);
        });
    }
    // Apply filter for tags
    if (!empty($filters['tags'])) {
        $tags = is_array($filters['tags']) ? $filters['tags'] : [$filters['tags']];
        $query->whereHas('tags', function ($q) use ($tags) {
            $q->whereIn('name', $tags);
        });
    }
    return $query->latest()->paginate(10);
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

            if (!empty($data['featured_image']) && $data['featured_image'] instanceof UploadedFile) {
                $this->mediaService->attachMedia(
                    $post, 
                    $data['featured_image'], 
                    'featured_image', 
                    'media'
                );
            }

            if (!empty($data['fileUploads'])) {
                $files = is_array($data['fileUploads']) ? $data['fileUploads'] : [$data['fileUploads']];

                foreach ($files as $file) {
                    if ($file instanceof UploadedFile) {
                        $this->mediaService->attachMedia(
                            $post, 
                            $file, 
                            'fileUploads', 
                            'media'
                        );
                    }
                }
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
        // \Log::info('CHECK MODEL INSTANCE', [
        //     'id' => $post->id,
        //     'exists' => $post->exists,
        //     'class' => get_class($post),
        //     'attributes' => $post->getAttributes(),
        // ]);

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
            // \Log::info("message", ['postData' => $postData]);
            // $post->update($postData);
            $post->fill($postData);

            if ($post->isDirty()) {
                $post->save();
            } else {
                throw new \Exception('Data tidak berubah. Mungkin isinya sama seperti sebelumnya.');
            }

            if (!empty($data['tags'])) {
                $tags = collect($data['tags'])->flatten()->filter()->all();
                $post->tags()->sync($tags);
            }
            
            if (!empty($data['featured_image'])) {
                $this->mediaService->attachMedia($post, $data['featured_image'], 'featured_image', 'media', true);
            }

            DB::commit();
            return $post;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(Post $post)
    {
        $post->delete();
        return true;
    }

    public function bulkAction($selectedPosts, $action)
    {
        if (empty($selectedPosts) || empty($action)) {
            return ['status' => 'error', 'message' => 'Please select posts and action.'];
        }
        switch ($action) {
            case 'publish':
                Post::whereIn('id', $selectedPosts)->update(['status' => 'published']);
                return ['status' => 'success', 'message' => 'Posts published successfully.'];
            case 'draft':
                Post::whereIn('id', $selectedPosts)->update(['status' => 'draft']);
                return ['status' => 'success', 'message' => 'Posts set as draft successfully.'];
            case 'delete':
                Post::whereIn('id', $selectedPosts)->delete();
                return ['status' => 'success', 'message' => 'Posts deleted successfully.'];
        }
        return ['status' => 'error', 'message' => 'Invalid action.'];
    }

    // public function getPublicPosts($category = null)
    // {
    //     $query = Post::where('status', 'published')->with('user', 'category', 'tags');
    //     if ($category) {
    //         $query->whereHas('category', function ($q) use ($category) {
    //             $q->where('name', $category);
    //         });
    //     }
    //     return $query->latest()->paginate(10);
    // }

    public function getPublicPostDetail($slug)
    {
        $post = Post::where('slug', $slug)->where('status', 'published')->with('user', 'category', 'tags')->firstOrFail();
        $popularPosts = Post::where('status', 'published')->with('user', 'category', 'tags')->latest()->take(5)->get();
        $post->increment('views');
        return [$post, $popularPosts];
    }

    public function getPostBySlug($slug)
    {
        try {
            
            $query = Post::with(['user', 'category', 'tags', 'media']);;

            $query->where('slug', $slug);

            $sortField = $filters['sort'] ?? 'created_at';
            $sortDirection = $filters['direction'] ?? 'desc';
            $query->orderBy($sortField, $sortDirection);

            $post = $query->first();

            return $post;

        } catch (\Throwable $th) {
            \Log::error('Error in getPostBySlug: ' . $th->getMessage());
            return null;
        }
    }

    public function getPostById($id)
    {
        try {
            
            $query = Post::with(['user', 'category', 'tags', 'media']);
            
            $query->where('id', $id);

            $sortField = $filters['sort'] ?? 'created_at';
            $sortDirection = $filters['direction'] ?? 'desc';
            $query->orderBy($sortField, $sortDirection);

            $post = $query->first();

            return $post;

        } catch (\Throwable $th) {
            \Log::error('Error in getPostBySlug: ' . $th->getMessage());
            return null;
        }
    }


}
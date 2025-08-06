<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\PostService;
use App\Models\Tag;
use Database\Factories\PostFactory;

class PostSeeder extends Seeder
{
    protected $postService;

    // Gunakan dependency injection untuk PostService
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 50; $i++) {
            // Ambil data dari factory
            $data = \Database\Factories\PostFactory::new()->definition();
            $data['title'] = ($i+1) .'. '. $data['title'];
            // Tambahkan tags random
            $data['tags'] = Tag::inRandomOrder()->take(rand(1, 3))->pluck('id')->toArray();

            // Simpan lewat service
            $this->postService->store($data);
        }
    }
}

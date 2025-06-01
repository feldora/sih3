<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Services\PostService;
use App\Models\Tag;
use Database\Factories\PostFactory;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $service = new PostService();

        for ($i = 0; $i < 15; $i++) {
            // Ambil data dari factory
            $data = \Database\Factories\PostFactory::new()->definition();

            // Tambahkan tags random
            $data['tags'] = Tag::inRandomOrder()->take(rand(1, 3))->pluck('id')->toArray();
            $data['views'] = rand(500, 1000); // Tambahkan views random

            // Simpan lewat service
            $service->store($data);
        }
    }
}

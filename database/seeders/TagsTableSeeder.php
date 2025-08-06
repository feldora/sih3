<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags_hidrologi = ['sungai','curah hujan','banjir','pengelolaan sumber daya air','daerah aliran sungai','sistem hidrologi','evaporasi','infiltrasi','limbah cair','irigasi'];
        $tags_hidrometeorologi = ['perubahan iklim','cuaca ekstrem','meteorologi','analisis hujan','ramalan cuaca','model hidrometeorologi','radar cuaca','angin','temperatur','tinggi muka air'];
        $tags_hidrogeologi = ['air tanah','sumber daya air bawah tanah','geohidrologi','akuifer','permeabilitas tanah','penurunan muka air tanah','kualitas air tanah','kontaminasi air tanah','pencemaran air bawah tanah','pengelolaan air tanah'];
        $data_h3 = ['data tinggi muka air', 'data debit', 'data sedimen', 'data klimatologi', 'data curah hujan', 'data muka air tanah', 'data minatan hidrogeologi', 'data kualitas air tanah', 'cekungan air tanah', 'data hidrogeologi'];
        $all_tags = array_merge($tags_hidrologi, $tags_hidrometeorologi, $tags_hidrogeologi, $data_h3);

        $tags_to_insert = array_map(function ($tag) {
            return ['name' => $tag, 'created_at' => now(), 'updated_at' => now()];
        }, $all_tags);

        DB::table('tags')->insert($tags_to_insert);

    }
}

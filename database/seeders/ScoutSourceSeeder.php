<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ScoutSource;

class ScoutSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            [
                'name' => 'TechCrunch AI',
                'url' => 'https://techcrunch.com/category/artificial-intelligence/feed/',
                'type' => 'rss',
                'is_active' => true,
            ],
            [
                'name' => 'VentureBeat AI',
                'url' => 'https://venturebeat.com/category/ai/feed/',
                'type' => 'rss',
                'is_active' => true,
            ],
            [
                'name' => 'The Verge AI',
                'url' => 'https://www.theverge.com/ai-artificial-intelligence/rss/index.xml',
                'type' => 'rss',
                'is_active' => true,
            ],
        ];

        foreach ($sources as $source) {
            ScoutSource::updateOrCreate(['url' => $source['url']], $source);
        }
    }
}

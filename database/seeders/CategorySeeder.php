<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Seed các category mặc định cho hệ thống (trong nước, nước ngoài, biển đảo,...).
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Du lịch trong nước',
                'slug' => 'du-lich-trong-nuoc',
                'description' => 'Các tour du lịch trong nước Việt Nam',
                'image_url' => 'https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?auto=format&fit=crop&w=640&q=60',
            ],
            [
                'name' => 'Du lịch nước ngoài',
                'slug' => 'du-lich-nuoc-ngoai',
                'description' => 'Các tour du lịch quốc tế',
                'image_url' => 'https://images.unsplash.com/photo-1494475673543-6a6a27143b13?auto=format&fit=crop&w=640&q=60',
            ],
            [
                'name' => 'Du lịch biển đảo',
                'slug' => 'du-lich-bien-dao',
                'description' => 'Các tour du lịch biển và đảo',
                'image_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=640&q=60',
            ],
            [
                'name' => 'Du lịch văn hóa',
                'slug' => 'du-lich-van-hoa',
                'description' => 'Các tour du lịch khám phá văn hóa',
                'image_url' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=640&q=60',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}


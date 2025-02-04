<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = ['Beach Resort','Resort', 'Budget Hotel', 'Boutique Hotel', 'Luxury Hotel', 'Business Hotel', 'Apartment Hotel'];

        foreach ($categories as $category) {
            Category::updateOrCreate(['name' => $category]);
        }
    }
}
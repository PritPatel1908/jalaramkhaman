<?php

namespace Database\Seeders;

use App\Enums\UnitIn;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category1 = Category::create([
            'name' => 'Jalaram Special',
            'code' => 'JalaramSpecial',
            'description' => 'Jalaram Special Products For Customer'
        ]);

        $product1 = Product::create([
            'name' => 'Gujarati Fafda',
            'code' => 'GujaratiFafda',
            'category_id' => $category1->id,
            'description' => 'Gujarati Fafda',
            'stock' => 1,
            'product_image_path' => ''
        ]);
        $product1->business_type_product_price()->create([
            'price' => '239',
            'product_id' => $product1->id,
            'per' => '200',
            'unit_in' => UnitIn::GRAM
        ]);

        $product1->customer_type_product_price()->create([
            'price' => '249',
            'product_id' => $product1->id,
            'per' => '200',
            'unit_in' => UnitIn::GRAM
        ]);

        $product2 = Product::create([
            'name' => 'Jalebi',
            'code' => 'Jalebi',
            'category_id' => 1,
            'description' => 'Jalebi',
            'stock' => 1,
            'product_image_path' => ''
        ]);

        $product2->business_type_product_price()->create([
            'price' => '149',
            'product_id' => $product2->id,
            'per' => '200',
            'unit_in' => UnitIn::GRAM
        ]);

        $product2->customer_type_product_price()->create([
            'price' => '159',
            'product_id' => $product2->id,
            'per' => '200',
            'unit_in' => UnitIn::GRAM
        ]);
    }
}

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
            'name' => 'Khaman',
            'code' => 'KH',
            'category_id' => $category1->id,
            'description' => 'Gujrati Khaman Dhokla is a popular traditional snack in Gujarat. It is made from besan and sooji. It is a steamed cake and has a mild tangy and sweet taste. It is soft and spongy and is usually served with green chutney and sweet chutney.',
            'stock' => 1,
            'product_image_path' => ''
        ]);
        $product1->business_type_product_price()->create([
            'price' => '70',
            'product_id' => $product1->id,
            'per' => '1',
            'unit_in' => UnitIn::KG
        ]);

        $product1->customer_type_product_price()->create([
            'price' => '80',
            'product_id' => $product1->id,
            'per' => '1',
            'unit_in' => UnitIn::KG
        ]);

        $product2 = Product::create([
            'name' => 'Without Fry Samosa',
            'code' => 'WFS',
            'category_id' => $category1->id,
            'description' => 'Without Fry Samosa is a popular traditional snack in Gujarat. It is made from besan and sooji. It is a steamed cake and has a mild tangy and sweet taste. It is soft and spongy and is usually served with green chutney and sweet chutney.',
            'stock' => 1,
            'product_image_path' => ''
        ]);

        $product2->business_type_product_price()->create([
            'price' => '55',
            'product_id' => $product2->id,
            'per' => '1',
            'unit_in' => UnitIn::NO
        ]);

        $product2->customer_type_product_price()->create([
            'price' => '60',
            'product_id' => $product2->id,
            'per' => '1',
            'unit_in' => UnitIn::NO
        ]);

        $product3 = Product::create([
            'name' => 'Cheese Samosa',
            'code' => 'CS',
            'category_id' => $category1->id,
            'description' => 'Cheese Samosa is a popular traditional snack in Gujarat. It is made from besan and sooji. It is a steamed cake and has a mild tangy and sweet taste. It is soft and spongy and is usually served with green chutney and sweet chutney.',
            'stock' => 1,
            'product_image_path' => ''
        ]);

        $product3->business_type_product_price()->create([
            'price' => '180',
            'product_id' => $product2->id,
            'per' => '400',
            'unit_in' => UnitIn::GRAM
        ]);

        $product3->customer_type_product_price()->create([
            'price' => '200',
            'product_id' => $product2->id,
            'per' => '400',
            'unit_in' => UnitIn::GRAM
        ]);
    }
}

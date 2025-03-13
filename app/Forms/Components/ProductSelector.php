<?php

namespace App\Forms\Components;

use App\Enums\UnitIn;
use App\Models\Product;
use Filament\Forms\Components\Field;
use Illuminate\Support\Facades\Auth;

class ProductSelector extends Field
{
    protected string $view = 'forms.components.product-selector';

    public function getProducts()
    {
        $products = Product::with(['customer_type_product_price', 'business_type_product_price'])->get();
        $formattedProducts = [];

        foreach ($products as $product) {
            $userType = Auth::user()->user_type;
            $price = $userType == 'business'
                ? $product->business_type_product_price->first()
                : $product->customer_type_product_price->first();

            if ($price) {
                $formattedProducts[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image_url' => asset('storage/' . $product->product_image_path) ?? '/images/default-product.jpg',
                    'price' => $price->price ?? 0,
                    'per' => $price->per ?? 1,
                    'unit_in' => $price->unit_in ?? 1,
                    'unit_label' => UnitIn::from($price->unit_in ?? 1)->getLabel(),
                    'description' => $product->description ?? '',
                ];
            }
        }

        return $formattedProducts;
    }
}

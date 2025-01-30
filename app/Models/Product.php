<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Product extends Model
{
    protected $table = 'products';

    /**
     * The "booted" method of the model.
     */

    protected $casts = [
        'category_id' => 'int',
        'stock' => 'float'
    ];

    protected $fillable = [
        'name',
        'code',
        'category_id',
        'description',
        'stock',
        'product_image_path',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

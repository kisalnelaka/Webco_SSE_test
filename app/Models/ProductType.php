<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    protected $fillable = ['name', 'api_unique_number'];

    public function categories()
    {
        return $this->morphedByMany(ProductCategory::class, 'categorizable', 'category_type');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_type');
    }
}
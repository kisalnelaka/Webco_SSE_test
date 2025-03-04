<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    protected $fillable = ['name', 'api_unique_number'];

    public function productCategories()
    {
        return $this->belongsToMany(ProductCategory::class, 'product_category_product_type');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_product_type');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $fillable = ['name', 'description', 'url'];

    public function productTypes()
    {
        return $this->belongsToMany(ProductType::class, 'product_category_product_type');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_product_category');
    }
}
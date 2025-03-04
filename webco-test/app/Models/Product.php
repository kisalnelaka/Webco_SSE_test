<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'product_color_id'];

    public function productColor()
    {
        return $this->belongsTo(ProductColor::class);
    }

    public function productTypes()
    {
        return $this->belongsToMany(ProductType::class, 'product_product_type');
    }

    public function productCategories()
    {
        return $this->belongsToMany(ProductCategory::class, 'product_product_category');
    }
}
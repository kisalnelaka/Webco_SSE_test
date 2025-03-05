<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'product_color_id', 'category_id', 'address', 'address_status'];

    public function color()
    {
        return $this->belongsTo(ProductColor::class, 'product_color_id');
    }

    public function productTypes()
    {
        return $this->belongsToMany(ProductType::class, 'product_type');
    }

    public function categories()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }
}
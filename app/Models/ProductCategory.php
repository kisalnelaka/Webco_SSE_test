<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $fillable = ['name', 'description', 'url'];

    public function productTypes()
    {
        return $this->morphToMany(ProductType::class, 'categorizable', 'category_type');
    }
}
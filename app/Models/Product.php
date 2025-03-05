<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'product_color_id'];

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
        return $this->belongsToMany(ProductCategory::class, 'product_category')->whereIn('id', function ($query) {
            $query->select('categorizable_id')
                ->from('category_type')
                ->whereIn('product_type_id', $this->productTypes->pluck('id'))
                ->where('categorizable_type', ProductCategory::class);
        });
    }
}
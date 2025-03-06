<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Main product model with relationships and custom fields.
 * Handles status tracking and color mapping.
 */
class Product extends Model
{
    // Fields allowed for mass assignment
    protected $fillable = [
        'name',
        'description',
        'address',
        'address_status',
        'product_color_id',
        'category_id',
        'is_processed',
        'processed_at'
    ];

    // Colors for the status bar task
    protected $statusColors = [
        'active' => 'bg-green-500',
        'pending' => 'bg-yellow-500',
        'inactive' => 'bg-red-500'
    ];

    protected $casts = [
        'is_processed' => 'boolean',
        'processed_at' => 'datetime',
    ];

    /**
     * Relationship with Category model.
     * Using eager loading in list views for performance.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(ProductColor::class, 'product_color_id');
    }

    public function productTypes(): MorphToMany
    {
        return $this->morphToMany(ProductType::class, 'typeable');
    }

    /**
     * Custom method for status bar display.
     * Maps product status to Tailwind CSS classes.
     */
    public function getStatusBarColor(): string
    {
        return $this->statusColors[$this->status] ?? 'bg-gray-500';
    }

    /**
     * Handles status text display with proper greeting.
     * Used in our custom field implementation.
     */
    public function getStatusBarText(): string
    {
        return "Hello {$this->name}";
    }
}
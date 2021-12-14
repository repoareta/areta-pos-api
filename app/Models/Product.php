<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Product extends Model
{
    use HasFactory, Userstamps, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'initial_stock',
        'unit_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The category that belong to the product.
     */
    public function category()
    {
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id');
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}

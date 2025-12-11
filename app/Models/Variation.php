<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'variation_name',
        'price',
        'stock_quantity',
        'sku',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variantImages()
    {
        return $this->hasMany(VariantImage::class, 'product_variant_id');
    }
}


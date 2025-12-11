<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'image_path',
    ];

    public function variation()
    {
        return $this->belongsTo(Variation::class, 'product_variant_id');
    }
}


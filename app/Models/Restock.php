<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'variation_id',
        'supplier_id',
        'quantity_added',
        'cost_per_unit',
        'total_cost',
        'previous_stock',
        'new_stock',
        'restocked_at',
        'note',
    ];

    protected $casts = [
        'restocked_at' => 'datetime',
        'cost_per_unit' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variation()
    {
        return $this->belongsTo(Variation::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 
        'order_date', 
        'total_amount', 
        'status',
        'payment_method',
        'payment_status',
        'shipping_name',
        'shipping_email',
        'shipping_phone',
        'shipping_address',
        'shipping_cost',
        'tax',
    ];

    protected $casts = [
        'order_date' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity', 'price_at_purchase', 'variation_id')
            ->withTimestamps();
    }

    public function variations()
    {
        return $this->belongsToMany(Variation::class, 'order_product', 'order_id', 'variation_id')
            ->withPivot('quantity', 'price_at_purchase', 'product_id')
            ->withTimestamps();
    }
}

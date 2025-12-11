<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['product_name', 'description', 'price', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function carts()
    {
        return $this->belongsToMany(Cart::class)->withPivot('quantity');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot('quantity', 'price_at_purchase');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function variations()
    {
        return $this->hasMany(Variation::class);
    }

    public function restocks()
    {
        return $this->hasMany(Restock::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function allReviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function totalReviews()
    {
        return $this->reviews()->count();
    }
}

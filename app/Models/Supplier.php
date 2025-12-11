<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_name', 'contact', 'phone', 'address'];

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}

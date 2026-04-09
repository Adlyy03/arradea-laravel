<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'image',
    ];

    protected $casts = [
        'price' => 'float',
        'stock' => 'integer',
    ];

    // Default image fallback
    public function getImageAttribute($value)
    {
        return $value ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=500&h=500';
    }

    // ─── Relations ──────────────────────────────────────────────────────────────

    /** Product belongs to a store */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /** Product belongs to a category */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /** Product has many orders */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}

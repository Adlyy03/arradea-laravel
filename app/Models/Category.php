<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'parent_id',
        'is_featured',
        'sort_order'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Scopes
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    // Accessors
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : 'https://via.placeholder.com/300x200?text=' . urlencode($this->name);
    }

    // Methods
    public function getAllChildren()
    {
        $children = collect();

        foreach ($this->children as $child) {
            $children->push($child);
            $children = $children->merge($child->getAllChildren());
        }

        return $children;
    }

    public function getProductsCount()
    {
        $categoryIds = [$this->id];
        
        // Add all children IDs recursively
        $children = $this->getAllChildren();
        foreach ($children as $child) {
            $categoryIds[] = $child->id;
        }
        
        return \App\Models\Product::whereIn('category_id', $categoryIds)->count();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (!$category->slug) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'category_id',
        'judul',
        'synopsis',
        'foto',
        'penulis',
        'penerbit',
        'tahun',
        'nomor_rak',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function bookItems()
    {
        return $this->hasMany(BookItem::class);
    }

    public function availableItems()
    {
        return $this->hasMany(BookItem::class)->where('status', 'available');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
}

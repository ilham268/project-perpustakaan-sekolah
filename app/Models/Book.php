<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'tahun_pengadaan',
        'nomor_klasifikasi',
        'judul',
        'penulis',
        'penerbit',
        'tahun',
        'sumber_buku',
        'jenis_koleksi',
        'jumlah_eksemplar',
        'category_id',
        'nomor_rak',
        'synopsis',
        'foto',
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
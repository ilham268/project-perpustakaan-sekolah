<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinjamKelas extends Model
{
    protected $table = 'pinjam_kelas';

    protected $fillable = [
        'kategori_pinjam_id',
        'user_id',
        'book_id',
        'kode_buku',
        'status',
        'kondisi',
        'denda',
        'status_denda',
    ];

    protected $casts = [
        'denda' => 'integer',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriPinjam::class, 'kategori_pinjam_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
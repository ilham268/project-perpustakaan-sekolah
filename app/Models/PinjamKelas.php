<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinjamKelas extends Model
{
    protected $table = 'pinjam_kelas';

    protected $fillable = [
        'user_id',
        'book_id',
        'kode_buku',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
        'kondisi',
        'denda',
        'status_denda',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'datetime',
        'tanggal_kembali' => 'datetime',
        'denda' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
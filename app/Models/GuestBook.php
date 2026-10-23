<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestBook extends Model
{
    use HasFactory;

    protected $table = 'guest_books';

    protected $fillable = [
        'nama',
        'kelas',
        'jurusan',
        'keperluan',
        'metode_absensi',
    ];
}
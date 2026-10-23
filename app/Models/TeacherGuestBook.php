<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherGuestBook extends Model
{
    use HasFactory;

    protected $table = 'teacher_guest_books';

    protected $fillable = [
        'user_id',
        'nama',
        'keperluan',
        'metode_absensi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

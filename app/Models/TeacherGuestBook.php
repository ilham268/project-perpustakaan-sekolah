<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherGuestBook extends Model
{
    use HasFactory;

    protected $table = 'teacher_guest_books';

    protected $fillable = [
        'nama',
        'keperluan',
    ];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriPinjam extends Model
{
    protected $table = 'kategori_pinjams';
    
    protected $fillable = [
        'nama_kategori',
        'kelas',
    ];

    // Relasi ke User (siswa dalam kelas ini)
    public function siswas()
    {
        return $this->hasMany(User::class, 'kategori_pinjam_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriPinjam extends Model
{
    protected $table = 'kategori_pinjams';

    protected $fillable = [
        'nama_kategori',
        'kelas_id',
        'kelas',
    ];

    // Relasi ke tabel kelas berdasarkan kelas_id
    public function kelasData()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // Relasi ke data peminjaman kelas
    public function pinjamKelas()
    {
        return $this->hasMany(PinjamKelas::class, 'kategori_pinjam_id');
    }


    public function siswas()
    {
        return $this->hasMany(User::class, 'kelas', 'kelas');
    }
}
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nomor_identitas',
        'kelas',        // TAMBAHKAN INI
        'jurusan',      // TAMBAHKAN INI
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi ke Cart
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    
    // Relasi ke Loan (Peminjaman)
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
    
    // Accessor untuk menampilkan role sebagai 'Siswa'
    public function getRoleNameAttribute()
    {
        if ($this->role === 'siswa') {
            return 'Siswa';
        }
        
        $roles = [
            'admin' => 'Admin',
            'petugas' => 'Petugas',
        ];
        
        return $roles[$this->role] ?? ucfirst($this->role);
    }
    
    // Accessor untuk menampilkan kelas dan jurusan lengkap
    public function getKelasJurusanAttribute()
    {
        if ($this->kelas && $this->jurusan) {
            return "Kelas {$this->kelas} - {$this->jurusan}";
        }
        
        return '-';
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'jurusan',
    ];

    public static function formatLabel(?string $level, ?string $jurusan): string
    {
        $level = preg_replace('/\s+/', ' ', trim((string) $level));
        $jurusan = preg_replace('/\s+/', ' ', trim((string) $jurusan));
        $levels = ['X', 'XI', 'XII'];
        $normalizedLevel = strtoupper($level);
        $normalizedJurusan = strtoupper($jurusan);

        if (in_array($normalizedLevel, $levels, true)) {
            if (preg_match('/^(XII|XI|X)\s+/i', $jurusan, $matches)) {
                $jurusan = trim(substr($jurusan, strlen($matches[0])));
            }

            if (preg_match('/\s+(XII|XI|X)$/i', $jurusan, $matches)) {
                $jurusan = trim(substr($jurusan, 0, -strlen($matches[0])));
            }

            return trim($normalizedLevel . ' ' . strtoupper($jurusan));
        }

        if (in_array($normalizedJurusan, $levels, true)) {
            return trim($normalizedJurusan . ' ' . strtoupper($level));
        }

        if (preg_match('/^(.*?)\s+(XII|XI|X)$/i', $level, $matches)) {
            $legacyMajor = trim($matches[1]);

            if ($jurusan !== '' && stripos($legacyMajor, $jurusan) === false) {
                $legacyMajor = trim($jurusan . ' ' . $legacyMajor);
            }

            return trim(strtoupper($matches[2] . ' ' . $legacyMajor));
        }

        return trim(strtoupper($level . ' ' . $jurusan));
    }
}

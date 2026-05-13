<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnBook extends Model
{
    protected $fillable = [
        'loan_id',
        'tanggal_pengembalian',
        'kondisi',
        'denda',
        'status',
    ];

    protected $casts = [
        'tanggal_pengembalian' => 'date',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}

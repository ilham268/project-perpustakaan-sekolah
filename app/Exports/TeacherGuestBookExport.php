<?php

namespace App\Exports;

use App\Models\TeacherGuestBook;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TeacherGuestBookExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return TeacherGuestBook::latest()->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Guru',
            'Keperluan',
            'Tanggal & Waktu'
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->nama,
            $row->keperluan,
            $row->created_at ? $row->created_at->format('d-m-Y H:i') : '-',
        ];
    }
}
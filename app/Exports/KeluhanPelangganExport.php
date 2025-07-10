<?php

namespace App\Exports;

use App\Models\KeluhanPelanggan;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KeluhanPelangganExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return KeluhanPelanggan::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama',
            'Email',
            'Nomor HP',
            'Status',
            'Keluhan',
            'Tanggal Dibuat',
        ];
    }

    public function map($keluhan): array
    {
        return [
            $keluhan->id,
            $keluhan->nama,
            $keluhan->email,
            $keluhan->nomor_hp,
            $keluhan->status_keluhan,
            $keluhan->keluhan,
            $keluhan->created_at,
        ];
    }
} 
<?php

namespace App\Exports;

use App\Models\ItemUsage;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ItemUsageExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(protected Request $request) {}

    public function query()
    {
        $query = ItemUsage::with(['item', 'user'])->latest();

        if ($this->request->dari) {
            $query->whereDate('tanggal_ambil', '>=', $this->request->dari);
        }
        if ($this->request->sampai) {
            $query->whereDate('tanggal_ambil', '<=', $this->request->sampai);
        }
        if ($this->request->nama) {
            $query->where('nama_pengambil', 'like', '%' . $this->request->nama . '%');
        }
        if ($this->request->barang) {
            $query->where('id_barang', $this->request->barang);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'Nama Pengambil', 'Sebagai', 'Barang', 'Jumlah'];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            \Carbon\Carbon::parse($row->tanggal_ambil)->format('d/m/Y H:i'),
            $row->nama_pengambil ?? $row->user->name ?? '-',
            ucfirst($row->sebagai ?? '-'),
            $row->item->nama_barang ?? '-',
            $row->jumlah_ambil,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F97316']]],
        ];
    }
}
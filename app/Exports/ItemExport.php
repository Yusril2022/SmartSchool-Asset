<?php

namespace App\Exports;

use App\Models\Item;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ItemExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(protected Request $request) {}

    public function query()
    {
        $query = Item::with('cabinet.room')->latest();

        if ($this->request->search) {
            $query->where('nama_barang', 'like', '%' . $this->request->search . '%');
        }
        if ($this->request->jenis && $this->request->jenis !== 'semua') {
            $query->where('jenis_barang', $this->request->jenis);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['No', 'Kode', 'Nama Barang', 'Kategori', 'Jenis', 'Merk', 'Lemari', 'Ruangan', 'Stok', 'Batas Min', 'Kondisi', 'Harga'];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->kode_barang,
            $row->nama_barang,
            $row->kategori,
            ucfirst($row->jenis_barang),
            $row->merk ?? '-',
            $row->cabinet->nama_lemari ?? '-',
            $row->cabinet->room->nama_ruangan ?? '-',
            $row->stok_total,
            $row->batas_minimum,
            $row->kondisi ?? 'Baik',
            'Rp ' . number_format($row->harga, 0, ',', '.'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F97316']]],
        ];
    }
}
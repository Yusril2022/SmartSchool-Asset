<?php

namespace App\Exports;

use App\Models\IncomingItem;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IncomingItemExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(protected Request $request) {}

    public function query()
    {
        $query = IncomingItem::with(['item', 'admin'])->latest();

        if ($this->request->search) {
            $query->whereHas('item', fn($q) =>
                $q->where('nama_barang', 'like', '%' . $this->request->search . '%')
            );
        }
        if ($this->request->dari) {
            $query->whereDate('tanggal_masuk', '>=', $this->request->dari);
        }
        if ($this->request->sampai) {
            $query->whereDate('tanggal_masuk', '<=', $this->request->sampai);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['No', 'Tanggal Masuk', 'Kode Barang', 'Nama Barang', 'Kategori', 'Jumlah Masuk', 'Stok Sekarang', 'Dicatat Oleh'];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            \Carbon\Carbon::parse($row->tanggal_masuk)->format('d/m/Y'),
            $row->item->kode_barang ?? '-',
            $row->item->nama_barang ?? '-',
            $row->item->kategori ?? '-',
            $row->jumlah_masuk,
            $row->item->stok_total ?? '-',
            $row->admin->name ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F97316']]],
        ];
    }
}
<?php

namespace App\Exports;

use App\Models\Borrowing;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BorrowingExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(protected Request $request) {}

    public function query()
    {
        $query = Borrowing::with(['item', 'user', 'admin'])->latest();

        if ($this->request->status && $this->request->status !== 'semua') {
            if ($this->request->status === 'terlambat') {
                $query->where('status', 'dipinjam')
                      ->whereNotNull('tanggal_kembali')
                      ->where('tanggal_kembali', '<', now());
            } else {
                $query->where('status', $this->request->status);
            }
        }
        if ($this->request->search) {
            $query->whereHas('user', fn($q) =>
                $q->where('name', 'like', '%' . $this->request->search . '%')
            );
        }
        if ($this->request->dari) {
            $query->whereDate('tanggal_peminjaman', '>=', $this->request->dari);
        }
        if ($this->request->sampai) {
            $query->whereDate('tanggal_peminjaman', '<=', $this->request->sampai);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['No', 'Kode Peminjaman', 'Peminjam', 'Barang', 'Jumlah', 'Tujuan', 'Tgl Pinjam', 'Tgl Kembali', 'Status', 'Diproses Oleh'];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        // Tentukan status display
        $status = $row->status;
        if ($status === 'dipinjam' && $row->tanggal_kembali && \Carbon\Carbon::parse($row->tanggal_kembali)->isPast()) {
            $status = 'Terlambat';
        } else {
            $status = ucfirst($status);
        }

        return [
            $no,
            $row->kode_peminjaman,
            $row->user->name ?? '-',
            $row->item->nama_barang ?? '-',
            $row->jumlah_pinjam,
            $row->tujuan_pinjam ?? '-',
            \Carbon\Carbon::parse($row->tanggal_peminjaman)->format('d/m/Y'),
            $row->tanggal_kembali ? \Carbon\Carbon::parse($row->tanggal_kembali)->format('d/m/Y') : '-',
            $status,
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
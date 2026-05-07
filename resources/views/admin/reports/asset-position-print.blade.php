<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Posisi Aset — Smart School Assets</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        color: #111;
        background: white;
        padding: 30px;
    }

    /* HEADER DOKUMEN */
    .doc-header {
        text-align: center;
        border-bottom: 2px solid #111;
        padding-bottom: 12px;
        margin-bottom: 20px;
    }

    .doc-header h1 {
        font-size: 16px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .doc-header h2 {
        font-size: 13px;
        font-weight: 600;
        margin-top: 4px;
    }

    .doc-header p {
        font-size: 11px;
        color: #555;
        margin-top: 4px;
    }

    /* PER RUANGAN */
    .room-section {
        margin-bottom: 28px;
        page-break-inside: avoid;
    }

    .room-header {
        background: #1e3a5f;
        color: white;
        padding: 8px 12px;
        border-radius: 4px 4px 0 0;
    }

    .room-header h3 {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .room-header p {
        font-size: 10px;
        color: #cbd5e1;
        margin-top: 2px;
    }

    /* TABLE */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: #f1f5f9;
    }

    th {
        padding: 8px 10px;
        text-align: left;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    td {
        padding: 7px 10px;
        border: 1px solid #e2e8f0;
        vertical-align: middle;
        font-size: 11px;
        color: #334155;
    }

    tr:nth-child(even) td {
        background: #f8fafc;
    }

    .no-col {
        width: 35px;
        text-align: center;
    }

    .kode-col {
        width: 100px;
        font-family: 'Courier New', monospace;
        font-size: 10px;
    }

    .empty-row td {
        text-align: center;
        color: #94a3b8;
        font-style: italic;
        padding: 14px;
    }

    /* FOOTER */
    .doc-footer {
        margin-top: 30px;
        border-top: 1px solid #e2e8f0;
        padding-top: 12px;
        display: flex;
        justify-content: space-between;
        font-size: 10px;
        color: #94a3b8;
    }

    /* TOMBOL PRINT */
    .print-btn {
        position: fixed;
        top: 20px;
        right: 20px;
        display: flex;
        gap: 10px;
        z-index: 99;
    }

    .print-btn button {
        background: #f97316;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 8px;
        font-size: 13px;
        cursor: pointer;
        font-weight: 600;
    }

    .print-btn a {
        background: #f1f5f9;
        color: #374151;
        border: 1px solid #d1d5db;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        text-decoration: none;
    }

    @media print {
        .print-btn {
            display: none !important;
        }

        body {
            padding: 15px;
        }

        .room-section {
            page-break-inside: avoid;
        }
    }
    </style>
</head>

<body>

    <!-- TOMBOL PRINT -->
    <div class="print-btn">
        <a href="{{ route('asset-position.index') }}">← Kembali</a>
        <button onclick="window.print()">🖨️ Print / Save PDF</button>
    </div>

    <!-- HEADER DOKUMEN -->
    <div class="doc-header">
        <h1>Smart School Asset Management</h1>
        <h2>Laporan Posisi Aset Sekolah</h2>
        <p>Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</p>
    </div>

    <!-- PER RUANGAN -->
    @forelse($rooms as $room)
    <div class="room-section">

        <!-- HEADER RUANGAN -->
        <div class="room-header">
            <h3>{{ $room->nama_ruangan }}</h3>
            <p>
                Lemari:
                {{ $room->cabinets->pluck('nama_lemari')->join(', ') ?: '-' }}
            </p>
        </div>

        <!-- TABEL ASET -->
        <table>
            <thead>
                <tr>
                    <th class="no-col">No</th>
                    <th class="kode-col">Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Merk</th>
                    <th>Hasil Perolehan</th>
                    <th>Lemari</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($room->cabinets as $cabinet)
                @forelse($cabinet->items as $item)
                <tr>
                    <td class="no-col">{{ $no++ }}</td>
                    <td class="kode-col">{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->kategori }}</td>
                    <td>{{ $item->merk ?? '-' }}</td>
                    <td>{{ $item->hasil_perolehan ? ucfirst(str_replace('_', ' ', $item->hasil_perolehan)) : '-' }}</td>
                    <td>{{ $cabinet->nama_lemari }}</td>
                </tr>
                @empty
                @endforelse
                @endforeach

                @if($room->cabinets->every(fn($c) => $c->items->isEmpty()))
                <tr class="empty-row">
                    <td colspan="7">Tidak ada aset di ruangan ini</td>
                </tr>
                @endif
            </tbody>
        </table>

    </div>
    @empty
    <div style="text-align:center; padding: 40px; color: #94a3b8;">
        Tidak ada data aset yang ditemukan.
    </div>
    @endforelse

    <!-- FOOTER -->
    <div class="doc-footer">
        <span>Smart School Asset Management System</span>
        <span>Dicetak: {{ now()->format('d/m/Y H:i') }}</span>
    </div>

</body>

</html>
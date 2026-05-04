<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code — {{ $barang->nama_barang }}</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Arial', sans-serif;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 20px;
    }

    .card {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        padding: 24px;
        width: 280px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .school-name {
        font-size: 10px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 16px;
    }

    .qr-wrapper {
        background: white;
        padding: 8px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        display: inline-block;
        margin-bottom: 16px;
    }

    .qr-wrapper svg,
    .qr-wrapper img {
        display: block;
        width: 180px;
        height: 180px;
    }

    .item-name {
        font-size: 15px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 6px;
        line-height: 1.3;
    }

    .item-code {
        font-size: 11px;
        font-family: 'Courier New', monospace;
        color: #6b7280;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 4px 10px;
        display: inline-block;
        margin-bottom: 10px;
    }

    .item-badge {
        font-size: 10px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 20px;
        display: inline-block;
    }

    .badge-aset {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .badge-konsumsi {
        background: #dcfce7;
        color: #15803d;
    }

    .divider {
        border: none;
        border-top: 1px dashed #e5e7eb;
        margin: 14px 0;
    }

    .scan-hint {
        font-size: 9px;
        color: #9ca3af;
    }

    /* PRINT STYLES */
    @media print {
        body {
            background: white;
            padding: 0;
        }

        .no-print {
            display: none !important;
        }

        .card {
            box-shadow: none;
            border: 1.5px solid #d1d5db;
            margin: 0 auto;
        }
    }
    </style>
</head>

<body>

    <!-- TOMBOL PRINT & KEMBALI — tidak ikut terprint -->
    <div class="no-print" style="position:fixed; top:20px; right:20px; display:flex; gap:10px; z-index:99;">
        <a href="{{ route('items.index') }}"
            style="background:#f3f4f6; color:#374151; border:1px solid #d1d5db; padding:8px 16px; border-radius:8px; font-size:13px; text-decoration:none;">
            ← Kembali
        </a>
        <button onclick="window.print()"
            style="background:#f97316; color:white; border:none; padding:8px 20px; border-radius:8px; font-size:13px; cursor:pointer; font-weight:600;">
            🖨️ Print / Save PDF
        </button>
    </div>

    <!-- KARTU QR -->
    <div class="card">

        <p class="school-name">Smart School Assets</p>

        <!-- QR CODE -->
        <div class="qr-wrapper">
            {!! QrCode::size(180)->margin(1)->generate(url('/scan/' . $barang->kode_barang)) !!}
        </div>

        <!-- NAMA BARANG -->
        <p class="item-name">{{ $barang->nama_barang }}</p>

        <!-- KODE BARANG -->
        <p class="item-code">{{ $barang->kode_barang }}</p>

        <br>

        <!-- JENIS BADGE -->
        <span class="item-badge {{ $barang->jenis_barang === 'aset' ? 'badge-aset' : 'badge-konsumsi' }}">
            {{ $barang->jenis_barang === 'aset' ? 'Aset' : 'Habis Pakai' }}
        </span>

        <hr class="divider">

        <p class="scan-hint">Scan QR untuk melihat detail barang</p>

    </div>

</body>

</html>
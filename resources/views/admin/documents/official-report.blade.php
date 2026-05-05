<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Berita Acara Peminjaman Aset</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Times New Roman', Times, serif;
        font-size: 11pt;
        color: #000;
        /* F4 = 215.9mm x 330.2mm, padding disesuaikan */
        padding: 20px 50px 20px 50px;
        line-height: 1.4;
    }

    /* KOP */
    .kop-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }

    .kop-table td {
        vertical-align: middle;
    }

    .kop-logo {
        width: 75px;
        text-align: center;
        padding-right: 10px;
    }

    .kop-logo img {
        width: 70px;
        height: 70px;
    }

    .kop-text {
        text-align: center;
        padding: 0 4px;
    }

    .kop-text .instansi-1 {
        font-size: 10pt;
        font-weight: bold;
        text-transform: uppercase;
    }

    .kop-text .instansi-2 {
        font-size: 10pt;
        font-weight: bold;
        text-transform: uppercase;
    }

    .kop-text .instansi-3 {
        font-size: 10pt;
        font-weight: bold;
        text-transform: uppercase;
    }

    .kop-text .nama-sekolah {
        font-size: 13pt;
        font-weight: bold;
        text-transform: uppercase;
    }

    .kop-text .alamat {
        font-size: 8.5pt;
        margin-top: 1px;
    }

    .kop-border-wrap {
        margin-top: 6px;
    }

    .kop-border-thick {
        border-top: 3px solid #000;
    }

    .kop-border-thin {
        border-top: 1px solid #000;
        margin-top: 2px;
    }

    /* JUDUL */
    .judul {
        text-align: center;
        margin: 10px 0 2px;
    }

    .judul h2 {
        font-size: 12pt;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .judul .nomor {
        font-size: 10.5pt;
        margin-top: 2px;
    }

    /* PEMBUKA */
    .pembuka {
        margin: 10px 0;
        text-align: justify;
    }

    /* LIST PIHAK */
    .pihak-list {
        margin: 4px 0 4px 20px;
    }

    .pihak-list li {
        margin-bottom: 2px;
    }

    /* PARA PIHAK */
    .para-pihak {
        margin: 6px 0;
        text-align: justify;
    }

    /* TABEL BARANG */
    .tabel-barang {
        width: 100%;
        border-collapse: collapse;
        margin: 6px 0;
        font-size: 10.5pt;
    }

    .tabel-barang th {
        border: 1px solid #000;
        padding: 4px 6px;
        text-align: left;
        background-color: #f2f2f2;
        font-weight: bold;
    }

    .tabel-barang td {
        border: 1px solid #000;
        padding: 4px 6px;
        vertical-align: top;
    }

    .tabel-barang td.center {
        text-align: center;
    }

    /* KETENTUAN */
    .ketentuan-list {
        margin: 4px 0 4px 20px;
    }

    .ketentuan-list li {
        margin-bottom: 3px;
        text-align: justify;
    }

    /* PENUTUP */
    .penutup {
        margin: 8px 0;
        text-align: justify;
    }

    /* TTD */
    .ttd-table {
        width: 100%;
        margin-top: 16px;
        border-collapse: collapse;
    }

    .ttd-table td {
        width: 50%;
        text-align: center;
        vertical-align: top;
        padding: 0 16px;
    }

    .ttd-ruang {
        height: 60px;
    }

    .ttd-nama {
        border-top: 1px solid #000;
        padding-top: 3px;
        font-weight: bold;
        font-size: 11pt;
    }

    .ttd-jabatan {
        font-size: 10pt;
    }

    .section-title {
        font-weight: bold;
        margin: 8px 0 3px;
    }
    </style>
</head>

<body>

    {{-- KOP SURAT --}}
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                {{-- Logo harus ada di public/images/logo-jabar.png --}}
                <img src="{{ public_path('images/logo-jabar.png') }}" alt="Logo Jabar">
            </td>
            <td class="kop-text">
                <div class="instansi-1">Pemerintah Daerah Provinsi Jawa Barat</div>
                <div class="instansi-2">Dinas Pendidikan</div>
                <div class="instansi-3">Cabang Dinas Pendidikan Wilayah IV</div>
                <div class="nama-sekolah">SMA Negeri 3 Cikampek</div>
                <div class="alamat">Jl. Sumur Bandung Kaler No.165 Desa Dawuan Timur Kec. Cikampek Kab. Karawang</div>
                <div class="alamat">Website: sman3cikampek.sch.id &nbsp;|&nbsp; E-mail: sman3cikampek@gmail.com</div>
                <div class="alamat">KARAWANG – 41373</div>
            </td>
        </tr>
    </table>
    <div class="kop-border-wrap">
        <div class="kop-border-thick"></div>
        <div class="kop-border-thin"></div>
    </div>

    {{-- JUDUL --}}
    <div class="judul">
        <h2>Berita Acara Serah Terima Peminjaman Aset</h2>
        <div class="nomor">
            Nomor :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/PK.11.01.01/SMAN3CKP/Cadisdik.Wil.IV
        </div>
    </div>

    {{-- PEMBUKA --}}
    <div class="pembuka">
        Pada hari ini,
        <strong>{{ \Carbon\Carbon::parse($borrowing->tanggal_peminjaman)->translatedFormat('l') }}</strong>,
        tanggal <strong>{{ \Carbon\Carbon::parse($borrowing->tanggal_peminjaman)->translatedFormat('d F Y') }}</strong>,
        bertempat di <strong>SMA Negeri 3 Cikampek</strong>, telah dilakukan serah terima barang/aset
        pinjaman antara:
    </div>

    {{-- PIHAK --}}
    <ol class="pihak-list">
        <li><strong>Nama</strong>: {{ $penandatangan?->name ?? '____________________________' }}</li>
        <li><strong>Jabatan</strong>: {{ $penandatangan?->jabatan ?? 'Kepala Sekolah / Wakasek Sarana dan Prasarana' }}
        </li>
        <li><strong>Unit Kerja</strong>: SMA Negeri 3 Cikampek</li>
        <li><strong>Nama</strong>: {{ $user->name }}</li>
        <li><strong>Jabatan</strong>: {{ ucfirst($user->role) }}</li>
        <li><strong>Unit Kerja</strong>: SMA Negeri 3 Cikampek</li>
    </ol>

    {{-- PARA PIHAK --}}
    <div class="para-pihak">
        PARA PIHAK dengan ini menerangkan bahwa <strong>PIHAK PERTAMA</strong> menyerahkan barang/aset
        kepada <strong>PIHAK KEDUA</strong>, dan <strong>PIHAK KEDUA</strong> menerima dalam keadaan baik
        dengan rincian berikut:
    </div>

    {{-- TABEL BARANG --}}
    <div class="section-title">Daftar Barang yang Dipinjam:</div>
    <table class="tabel-barang">
        <thead>
            <tr>
                <th style="width:25px;">No</th>
                <th>Nama Barang</th>
                <th>Merk / Kode</th>
                <th>Jumlah</th>
                <th>Kondisi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="center">1.</td>
                <td>{{ $item->nama_barang }}</td>
                <td>{{ $item->merk ?? '-' }} / {{ $item->kode_barang }}</td>
                <td class="center">{{ $borrowing->jumlah_pinjam }} unit</td>
                <td>{{ $item->kondisi ?? 'Baik' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- KETENTUAN --}}
    <div class="section-title">Ketentuan Peminjaman:</div>
    <ol class="ketentuan-list">
        <li>
            <strong>Jangka Waktu</strong>: Peminjaman berlaku mulai dari tanggal
            <strong>{{ \Carbon\Carbon::parse($borrowing->tanggal_peminjaman)->format('d F Y') }}</strong>
            sampai dengan
            <strong>
                @if ($borrowing->tanggal_kembali)
                {{ \Carbon\Carbon::parse($borrowing->tanggal_kembali)->format('d F Y') }}
                @else
                ditentukan kemudian
                @endif
            </strong>.
        </li>
        <li>
            <strong>Tujuan</strong>: Barang digunakan semata-mata untuk keperluan
            <strong>{{ $borrowing->tujuan_pinjam ?? '____________________________' }}</strong>.
        </li>
        <li>
            <strong>Tanggung Jawab</strong>: PIHAK KEDUA bertanggung jawab penuh atas perawatan,
            keamanan, dan keutuhan barang selama masa pinjaman.
        </li>
        <li>
            <strong>Kerusakan/Kehilangan</strong>: Jika terjadi kerusakan atau kehilangan,
            PIHAK KEDUA bersedia memperbaiki atau mengganti sesuai kebijakan sekolah.
        </li>
        <li>
            <strong>Pengembalian</strong>: PIHAK KEDUA wajib mengembalikan barang dalam kondisi
            baik saat masa pinjaman berakhir.
        </li>
    </ol>

    {{-- PENUTUP --}}
    <div class="penutup">
        Demikian Berita Acara Serah Terima ini dibuat dalam rangkap 2 (dua) untuk dipergunakan
        sebagaimana mestinya.
    </div>

    {{-- TANDA TANGAN --}}
    <table class="ttd-table">
        <tr>
            <td><strong>PIHAK KEDUA (Peminjam)</strong></td>
            <td><strong>PIHAK PERTAMA (Pemberi)</strong></td>
        </tr>
        <tr>
            <td class="ttd-ruang"></td>
            <td class="ttd-ruang"></td>
        </tr>
        <tr>
            <td>
                <div class="ttd-nama">( {{ $user->name }} )</div>
                <div class="ttd-jabatan">{{ ucfirst($user->role) }}</div>
            </td>
            <td>
                <div class="ttd-nama">( {{ $penandatangan?->name ?? '____________________________' }} )</div>
                <div class="ttd-jabatan">{{ $penandatangan?->jabatan ?? 'Kepala Sekolah' }}</div>
            </td>
        </tr>
    </table>

</body>

</html>
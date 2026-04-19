@extends('layouts.user')

@section('content')

<div class="max-w-3xl mx-auto space-y-6">

    <!-- HEADER -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">
            Scan QR Barang
        </h1>
        <p class="text-gray-500 text-sm">
            Arahkan kamera ke QR code untuk memproses barang
        </p>
    </div>

    <!-- CARD -->
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">

        <!-- READER -->
        <div class="flex justify-center">
            <div id="reader" class="w-full max-w-md"></div>
        </div>

        <!-- INFO -->
        <div class="mt-4 text-center text-sm text-gray-500">
            Pastikan QR berada di tengah kotak untuk hasil terbaik
        </div>

    </div>

</div>

@endsection

@section('scripts')

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
document.addEventListener("DOMContentLoaded", async function () {

    const html5QrCode = new Html5Qrcode("reader");

    function onScanSuccess(decodedText) {
        window.location.href = decodedText;
    }

    const devices = await Html5Qrcode.getCameras();

    if (devices.length) {

        let cameraId = devices[0].id;

        // pilih kamera belakang kalau ada
        devices.forEach(device => {
            if (device.label.toLowerCase().includes("back") ||
                device.label.toLowerCase().includes("rear")) {
                cameraId = device.id;
            }
        });

        html5QrCode.start(
            cameraId,
            {
                fps: 15,
                qrbox: { width: 280, height: 280 }, // lebih proporsional
                aspectRatio: 1.0
            },
            onScanSuccess
        );
    }

});
</script>

@endsection
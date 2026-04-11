@extends('layouts.app')

@section('title', 'Scanner Kode QR Kwitansi')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <div class="max-w-2xl mx-auto">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-bedas-100 text-bedas-600 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Scanner Kode QR</h2>
                <p class="text-gray-500 mt-2">Arahkan kamera perangkat Anda ke Kode QR pada Kwitansi BKU untuk memverifikasi keasliannya.</p>
            </div>

            <!-- Scanner Box -->
            <div class="relative bg-black rounded-2xl overflow-hidden shadow-inner flex flex-col justify-center items-center" style="min-height: 400px;">
                <div id="reader" width="100%" class="w-full h-full"></div>
                <!-- Initial Placeholder -->
                <div id="scanner-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-white bg-black bg-opacity-70 z-10 pointer-events-none">
                    <svg class="w-16 h-16 text-gray-400 mb-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="font-medium tracking-wide">Meminta Akses Kamera...</p>
                </div>
            </div>

            <!-- Found Details Box (Hidden by default) -->
            <div id="result-box" class="mt-6 hidden animate-bounce p-4 bg-green-50 rounded-xl border border-green-200">
                <div class="flex items-center text-green-700">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-bold">Kode QR Berhasil Dibaca!</p>
                        <p class="text-sm">Sedang mengalihkan ke halaman verifikasi...</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-6 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bedas-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Customize the HTML5 QRCode styling to fit Tailwind theme */
    #reader {
        border: none !important;
    }
    #reader__dashboard_section_csr button {
        background-color: #16a34a !important; /* Bedas 600 */
        color: white !important;
        border: none !important;
        padding: 8px 16px !important;
        border-radius: 6px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        margin-top: 10px !important;
    }
    #reader__dashboard_section_csr button:hover {
        background-color: #15803d !important; /* Bedas 700 */
    }
    #reader select {
        padding: 8px !important;
        border-radius: 6px !important;
        border: 1px solid #d1d5db !important;
        margin-bottom: 10px !important;
        max-width: 90% !important;
    }
    #reader__camera_selection {
        color: black !important;
    }
    /* Hide some unnecessary text */
    #reader__dashboard_section_swaplink {
        display: none !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let isRedirecting = false;
        const html5QrCode = new Html5Qrcode("reader");

        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            if (isRedirecting) return;
            isRedirecting = true;
            
            // Stop scanning
            html5QrCode.stop().then((ignore) => {
                // Remove placeholder
                document.getElementById('scanner-placeholder').style.display = 'none';
                
                // Show result box
                document.getElementById('result-box').classList.remove('hidden');
                
                // Assume the decoded text is the URL for the verify endpoint.
                // The URL is generated via 'route('bku.verify', hash)'
                if(decodedText.startsWith('http')) {
                    window.location.href = decodedText;
                } else {
                    alert('QR Code tidak sesuai dengan format sistem.');
                    window.location.reload();
                }
            }).catch((err) => {
                console.error("Failed to stop scanning.", err);
            });
        };

        const config = { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 };
        
        html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback)
            .then(() => {
                document.getElementById('scanner-placeholder').style.display = 'none';
            })
            .catch((err) => {
                console.error('Camera access failed', err);
                document.getElementById('scanner-placeholder').innerHTML = `
                    <div class="text-center text-red-500">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <p class="font-bold">Akses Kamera Gagal</p>
                        <p class="text-sm mt-1 text-red-400">Pastikan Anda telah memberikan izin kamera pada browser</p>
                    </div>
                `;
            });
    });
</script>
@endpush

<x-filament-panels::page>
    @php $stats = $this->getStats(); @endphp

    <div
        x-data="{
            show: false,
            mode: 'checkin',
            stream: null,
            openCamera(mode) {
                this.mode = mode;
                this.show = true;
                this.$nextTick(() => this.startCamera());
            },
            closeCamera() {
                this.stopCamera();
                this.show = false;
            },
            startCamera() {
                const video = this.$refs.video;
                navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } })
                    .then(stream => {
                        this.stream = stream;
                        video.srcObject = stream;
                        video.play();
                    })
                    .catch(err => {
                        $wire.dispatch('notification', { title: 'Gagal membuka kamera: ' + err.message, type: 'danger' });
                        this.closeCamera();
                    });
            },
            stopCamera() {
                if (this.stream) {
                    this.stream.getTracks().forEach(t => t.stop());
                    this.stream = null;
                }
            },
            capture() {
                const video = this.$refs.video;
                const canvas = this.$refs.canvas;
                const ctx = canvas.getContext('2d');

                const w = video.videoWidth || 640;
                const h = video.videoHeight || 480;
                canvas.width = w;
                canvas.height = h;
                ctx.drawImage(video, 0, 0, w, h);

                const now = new Date();
                const pad = (n) => String(n).padStart(2, '0');
                const ts = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())} ${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;

                ctx.font = 'bold 22px monospace';
                ctx.textBaseline = 'bottom';
                const tsW = ctx.measureText(ts).width;
                ctx.fillStyle = 'rgba(0,0,0,0.65)';
                ctx.fillRect(10, h - 50, tsW + 20, 40);
                ctx.fillStyle = '#ffffff';
                ctx.fillText(ts, 20, h - 18);

                const base64 = canvas.toDataURL('image/png');
                this.closeCamera();

                const method = this.mode === 'checkin' ? 'checkin' : 'checkout';
                $wire[method](base64).then(() => window.location.reload());
            }
        }"
        class="space-y-6"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-filament::section>
                <x-slot name="heading">Status Hari Ini</x-slot>
                <div class="text-center space-y-4">
                    @if(!$stats['today_checked_in'])
                        <p class="text-danger-600 text-lg font-bold">&#10007; Belum Check In</p>
                        <p class="text-sm text-gray-500">Gunakan tombol <strong>Check In</strong> di pojok kanan bawah</p>
                    @elseif(!$stats['today_checked_out'])
                        <p class="text-success-600 text-lg font-bold">&#10003; Sudah Check In</p>
                        <p class="text-sm text-gray-500">Gunakan tombol <strong>Check Out</strong> di pojok kanan bawah</p>
                    @else
                        <p class="text-success-600 text-lg font-bold">&#10003; Selesai</p>
                        <p class="text-sm text-gray-500">Sudah Check In & Check Out hari ini</p>
                    @endif
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">Absensi Bulan Ini</x-slot>
                <div class="text-center">
                    <p class="text-primary-600 text-3xl font-bold">{{ $stats['this_month_attendance'] }}</p>
                    <p class="text-sm text-gray-500">Total Hadir</p>
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">Keterlambatan</x-slot>
                <div class="text-center">
                    <p class="text-warning-600 text-3xl font-bold">{{ $stats['this_month_late'] }}</p>
                    <p class="text-sm text-gray-500">Bulan Ini</p>
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">Koreksi Tertunda</x-slot>
                <div class="text-center">
                    <p class="text-danger-600 text-3xl font-bold">{{ $stats['pending_corrections'] }}</p>
                    <p class="text-sm text-gray-500">Menunggu Persetujuan</p>
                </div>
            </x-filament::section>
        </div>

        {{-- Floating Buttons --}}
        <div x-show="!show" class="fixed bottom-6 right-6 z-40 flex flex-col gap-3">
            <button
                type="button"
                x-show="@js(!$stats['today_checked_in'])"
                x-on:click="openCamera('checkin')"
                class="flex items-center gap-2 rounded-full bg-success-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg hover:bg-success-500 hover:shadow-xl hover:scale-105 active:scale-95 transition-all"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                Check In
            </button>
            <button
                type="button"
                x-show="@js($stats['today_checked_in'] && !$stats['today_checked_out'])"
                x-on:click="openCamera('checkout')"
                class="flex items-center gap-2 rounded-full bg-warning-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg hover:bg-warning-500 hover:shadow-xl hover:scale-105 active:scale-95 transition-all"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 5l-4 4 4 4"/>
                </svg>
                Check Out
            </button>
        </div>

        {{-- Camera Modal --}}
        <div
            x-show="show"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/70"
            x-on:keydown.escape.window="closeCamera()"
        >
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 max-w-md w-full mx-4" @click.outside="closeCamera()">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="mode === 'checkin' ? 'Check In' : 'Check Out'"></h3>
                    <button type="button" @click="closeCamera()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 text-2xl leading-none">&times;</button>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Arahkan kamera ke wajah Anda, lalu tekan tombol ambil foto.</p>

                <div class="relative bg-black rounded-lg overflow-hidden mb-4" style="aspect-ratio:4/3;">
                    <video x-ref="video" autoplay playsinline class="w-full h-full object-cover"></video>
                    <div class="absolute bottom-3 left-3 bg-black/60 text-white text-xs font-mono px-2 py-1 rounded" x-text="new Date().toLocaleString('id-ID')"></div>
                </div>

                <canvas x-ref="canvas" class="hidden"></canvas>

                <div class="flex gap-3">
                    <button type="button" @click="closeCamera()" class="flex-1 px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Batal
                    </button>
                    <button type="button" @click="capture()" class="flex-1 px-4 py-2.5 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-500 transition-colors flex items-center justify-center gap-2">
                        <span>&#128247;</span> Ambil Foto
                    </button>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        const flash = @json(session('notification'));
        if (flash) {
            $wire.dispatch('notification', { title: flash.title, type: flash.type });
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(pos => {
                $wire.set('checkinLatitude', pos.coords.latitude);
                $wire.set('checkinLongitude', pos.coords.longitude);
                $wire.set('checkinAccuracy', pos.coords.accuracy);
            }, err => console.warn('GPS error:', err.message));
        }
    </script>
    @endscript
</x-filament-panels::page>

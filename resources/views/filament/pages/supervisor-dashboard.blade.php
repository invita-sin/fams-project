<x-filament-panels::page>
    @php $stats = $this->getTeamStats(); @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-filament::section>
            <x-slot name="heading">Total Tim</x-slot>
            <div class="text-center">
                <p class="text-primary-600 text-3xl font-bold">{{ $stats['total_team'] }}</p>
                <p class="text-sm text-gray-500">Anggota</p>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Hadir Hari Ini</x-slot>
            <div class="text-center">
                <p class="text-success-600 text-3xl font-bold">{{ $stats['today_present'] }}</p>
                <p class="text-sm text-gray-500">Dari {{ $stats['total_team'] }} Anggota</p>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Koreksi Tertunda</x-slot>
            <div class="text-center">
                <p class="text-warning-600 text-3xl font-bold">{{ $stats['pending_approvals'] }}</p>
                <p class="text-sm text-gray-500">Menunggu Persetujuan</p>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Dokumentasi Hari Ini</x-slot>
            <div class="text-center">
                <p class="text-info-600 text-3xl font-bold">{{ $stats['today_documentations'] }}</p>
                <p class="text-sm text-gray-500">Total Foto</p>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>

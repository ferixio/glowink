<div
    style="padding: 24px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); font-family: Arial, sans-serif; color: #333;">
    <!-- Header -->
    <h2 style="font-size: 20px; font-weight: bold; margin-bottom: 16px;">🌐 Jaringan Mitra</h2>

    <!-- Statistik Overview -->
    <div
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 20px;">
        <div
            style="background-color: #ebf5ff; padding: 12px; border-radius: 8px; border: 1px solid #cce4ff; display: flex; align-items: center;">
            <div style="padding: 8px; background-color: #dceeff; border-radius: 6px;">
                <svg style="width: 24px; height: 24px; color: #2563eb;" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
            </div>
            <div style="margin-left: 10px;">
                <p style="font-size: 12px; color: #2563eb; margin: 0;">Total Downline</p>
                <p style="font-size: 18px; font-weight: bold; color: #1e3a8a; margin: 0;">{{ $totalDownline }}</p>
            </div>
        </div>

        @foreach ($levelStats as $stat)
            <button wire:click="filterByLevel({{ $stat->level }})"
                style="background-color: {{ $selectedLevel == $stat->level ? '#059669' : '#ecfdf5' }};
                       padding: 12px;
                       border-radius: 8px;
                       border: 1px solid #bbf7d0;
                       display: flex;
                       align-items: center;
                       cursor: pointer;
                       transition: all 0.2s;
                       {{ $selectedLevel == $stat->level ? 'color: white;' : '' }}"
                onmouseover="this.style.backgroundColor='{{ $selectedLevel == $stat->level ? '#047857' : '#d1fae5' }}'"
                onmouseout="this.style.backgroundColor='{{ $selectedLevel == $stat->level ? '#059669' : '#ecfdf5' }}'">
                <div
                    style="padding: 8px; background-color: {{ $selectedLevel == $stat->level ? '#10b981' : '#d1fae5' }}; border-radius: 6px;">
                    <svg style="width: 24px; height: 24px; color: {{ $selectedLevel == $stat->level ? 'white' : '#059669' }};"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div style="margin-left: 10px;">
                    <p
                        style="font-size: 12px; color: {{ $selectedLevel == $stat->level ? 'white' : '#059669' }}; margin: 0;">
                        Level {{ $stat->level }}</p>
                    <p
                        style="font-size: 18px; font-weight: bold; color: {{ $selectedLevel == $stat->level ? 'white' : '#064e3b' }}; margin: 0;">
                        {{ $stat->total }}</p>
                </div>
            </button>
        @endforeach
    </div>

    <!-- Filter dan Search -->
    <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
        <div style="flex: 1;">
            <label for="search" style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Cari
                Mitra</label>
            <input wire:model.live="search" type="text" id="search"
                placeholder="Cari berdasarkan nama atau email..."
                style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
        </div>
        <div>
            <label for="level" style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Filter
                Level</label>
            <select wire:model.live="selectedLevel" id="level"
                style="width: 150px; padding: 8px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
                <option value="">Semua Level</option>
                @foreach ($availableLevels as $level)
                    <option value="{{ $level }}">Level {{ $level }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="perPage" style="font-size: 13px; font-weight: 500; margin-bottom: 4px; display: block;">Per
                Halaman</label>
            <select wire:model.live="perPage" id="perPage"
                style="width: 100px; padding: 8px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        @if ($showLevelFilter)
            <div>
                <button wire:click="clearLevelFilter"
                    style="background-color: #ef4444; color: white; padding: 8px 16px; border: none; border-radius: 6px; font-size: 14px; cursor: pointer; transition: background 0.2s;"
                    onmouseover="this.style.backgroundColor='#dc2626'"
                    onmouseout="this.style.backgroundColor='#ef4444'">
                    Clear Filter
                </button>
            </div>
        @endif
    </div>

    <!-- Tabel Data -->
    <div class="space-y-3">
        @forelse($jaringanMitra as $index => $item)
            <div class="p-4 transition bg-white rounded-lg shadow hover:shadow-md">
                <div class="flex items-center justify-between">
                    {{-- Kiri: ID Mitra & Nama --}}
                    <div>
                        <p class="text-lg font-bold text-blue-500">
                            {{ $item->user->nama ?? 'N/A' }}
                        </p>
                        <p class="text-sm font-medium text-gray-700">
                            {{ $item->user->id_mitra }} ( {{ $item->user->id }} )
                        </p>

                        <div>
                            @if ($item->user->status_qr ?? false)
                                <span class="inline-block px-4 py-1 text-xs text-green-700 bg-green-100 rounded-full">
                                    QR Aktif
                                </span>
                            @else
                                <span class="inline-block px-4 py-1 text-xs text-red-700 bg-red-100 rounded-full">
                                    QR non Aktif
                                </span>
                            @endif
                             <span class="inline-block px-2 py-1 text-xs text-gray-700 bg-gray-100 rounded-full">
                                {{ number_format($item->user->poin_reward , 0) ?? 0 }} Poin
                            </span>
                        </div>
                    </div>

                    {{-- Kanan: Poin & Status --}}
                    <div class="text-right">

                        <div class="mt-2">
                            <p class="text-xs text-gray-500 ">
                            ID Mitra Sponsor:<br> <strong
                                class="text-base font-bold text-green-600 ">{{ $item->user->sponsorWithMitra->id_mitra ?? 'N/A' }}</strong>
                        </p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-4 text-center text-gray-500 bg-white rounded-lg shadow">
                Belum ada data jaringan mitra
            </div>
        @endforelse
    </div>

</div>

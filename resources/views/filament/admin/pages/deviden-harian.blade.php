<x-filament-panels::page>
    <x-filament-panels::form>
        {{ $this->form }}
    </x-filament-panels::form>
    @if ($devidenHarian || $searchResults)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
            <a href="{{ route('filament.admin.pages.list-detail-deviden-harian', ['selectedDate' => $data['selectedDate'] ?? now()->format('Y-m-d')]) }}"
                class="bg-white rounded-xl shadow p-6 flex items-center">
                <div class="bg-blue-100 text-blue-600 rounded-full p-3 mr-4">
                    <x-heroicon-o-currency-dollar class="w-8 h-8" />
                </div>
                <div>
                    <div class="text-gray-500 text-sm">Omzet Aktivasi</div>
                    <div class="text-2xl font-bold">
                        <div class="text-blue-600 hover:underline">

                            {{ $devidenHarian ? $devidenHarian->omzet_aktivasi : $searchResults->omzet_aktivasi }}
                        </div>
                    </div>
                </div>
            </a>
            <a href="{{ route('filament.admin.pages.list-detail-repeat-order', ['selectedDate' => $data['selectedDate'] ?? now()->format('Y-m-d')]) }}"
                class="bg-white rounded-xl shadow p-6 flex items-center">
                <div class="bg-green-100 text-green-600 rounded-full p-3 mr-4">
                    <x-heroicon-o-currency-dollar class="w-8 h-8" />
                </div>
                <div>
                    <div class="text-gray-500 text-sm">Omzet RO Basic</div>
                    <div class="text-2xl font-bold">
                        {{ $devidenHarian ? $devidenHarian->omzet_ro_basic : $searchResults->omzet_ro_basic }}
                    </div>
                </div>
            </a>
            <a href="{{ route('filament.admin.pages.list-mitra-deviden-harian', ['selectedDate' => $data['selectedDate'] ?? now()->format('Y-m-d')]) }}"
                class="bg-white rounded-xl shadow p-6 flex items-center">
                <div class="bg-yellow-100 text-yellow-600 rounded-full p-3 mr-4">
                    <x-heroicon-o-users class="w-8 h-8" />
                </div>
                <div>
                    <div class="text-gray-500 text-sm">Total Member</div>
                    <div class="text-2xl font-bold">
                        <div class="text-yellow-600 hover:underline">
                            {{ number_format($devidenHarian ? $devidenHarian->total_member : $searchResults->total_member, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </a>
            <div class="bg-white rounded-xl shadow p-6 flex items-center">
                <div class="bg-purple-100 text-purple-600 rounded-full p-3 mr-4">
                    <x-heroicon-o-gift class="w-8 h-8" />
                </div>
                <div>
                    <div class="text-gray-500 text-sm">Deviden Diterima</div>
                    <div class="text-2xl font-bold">Rp
                        {{ number_format($devidenHarian ? $devidenHarian->deviden_diterima : $searchResults->deviden_diterima, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-8 text-right text-gray-400 text-sm">
            @if ($devidenHarian)
                Data per {{ \Carbon\Carbon::parse($devidenHarian->created_at)->translatedFormat('d F Y H:i') }}
                <div class="mt-2 text-green-600 font-medium">✓ Data tersimpan di database</div>
            @elseif($searchResults)
                Data per {{ \Carbon\Carbon::parse($searchResults->created_at)->translatedFormat('d F Y H:i') }}
                <div class="mt-2 text-orange-600 font-medium">⚠ Data hasil pencarian (belum tersimpan)</div>
            @endif
        </div>
    @else
        <div class="p-8 text-center text-gray-400">
            {{-- Data deviden harian belum tersedia pada tanggal : {{ $this->data['selectedDate'] }}. --}}
            <div class="mt-2 text-sm">Klik tombol "Cari Data" untuk melihat perhitungan data.</div>
        </div>
    @endif

    <!-- Monthly status section -->
    <div class="mt-10 rounded-xl shadow w-full " style="width: 100%;">
        <div class="px-6 py-4 border-b">
            <div class="text-lg font-semibold text-gray-800">Status Deviden Bulan {{ $this->monthLabel }}</div>
            <div class="text-sm text-gray-500">Periode {{ $this->monthRangeLabel }}</div>
        </div>
        <div style="overflow-x:auto; width:100%; ">
            <table style="width:100%; border-collapse:collapse; font-family:Arial, sans-serif;">
                <thead>
                    <tr style="background-color:#f9fafb;">
                        <th
                            style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; border-bottom:1px solid #e5e7eb;">
                            Tanggal
                        </th>
                        <th
                            style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; border-bottom:1px solid #e5e7eb;">
                            Status
                        </th>
                        <th
                            style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; border-bottom:1px solid #e5e7eb;">
                            Jumlah Aktivasi
                        </th>
                        <th
                            style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; border-bottom:1px solid #e5e7eb;">
                            Jumlah RO Basic
                        </th>
                        <th
                            style="padding:12px 16px; text-align:left; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; border-bottom:1px solid #e5e7eb;">
                            Jumlah Mitra yang menerima
                        </th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->monthlyStatuses as $row)
                        <tr style="transition:background-color 0.2s ease;"
                            onmouseover="this.style.backgroundColor='#f3f4f6'"
                            onmouseout="this.style.backgroundColor='white'">
                            <td
                                style="padding:12px 16px; font-size:14px; color:#374151; border-bottom:1px solid #e5e7eb;">
                                {{ \Carbon\Carbon::parse($row['date'])->translatedFormat('d F Y') }}
                            </td>
                            <td style="padding:12px 16px; font-size:14px; border-bottom:1px solid #e5e7eb;">
                                @if ($row['processed'])
                                    <span
                                        style="display:inline-flex; align-items:center; padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:500; background-color:#d1fae5; color:#065f46;">
                                        Sudah diproses
                                    </span>
                                @else
                                    <span
                                        style="display:inline-flex; align-items:center; padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:500; background-color:#fee2e2; color:#991b1b;">
                                        Belum diproses
                                    </span>
                                @endif
                            </td>
                            <td style="padding:12px 16px; font-size:14px; border-bottom:1px solid #e5e7eb;">
                                {{ $row['omzet_aktivasi'] !== null ? number_format($row['omzet_aktivasi'], 0, ',', '.') : '' }}
                            </td>
                            <td style="padding:12px 16px; font-size:14px; border-bottom:1px solid #e5e7eb;">
                                {{ $row['omzet_ro_basic'] !== null ? number_format($row['omzet_ro_basic'], 0, ',', '.') : '' }}
                            </td>
                            <td style="padding:12px 16px; font-size:14px; border-bottom:1px solid #e5e7eb;">
                                {{ $row['total_member'] !== null ? number_format($row['total_member'], 0, ',', '.') : '' }}
                            </td>

                            {{-- <td style="padding:12px 16px; font-size:14px; border-bottom:1px solid #e5e7eb;">
                                {{ $row['total'] }}
                            </td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-filament-panels::page>

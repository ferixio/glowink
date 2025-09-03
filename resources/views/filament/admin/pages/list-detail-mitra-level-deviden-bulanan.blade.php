<x-filament-panels::page>
    <!-- Header with Back Button -->
    <div style="margin-bottom: 0rem;">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <a href="{{ route('filament.admin.pages.deviden-bulanan') }}"
                style="background: #6b7280; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; text-decoration: none; transition: background-color 0.2s;"
                onmouseover="this.style.backgroundColor='#4b5563'" onmouseout="this.style.backgroundColor='#6b7280'">
                ← Kembali ke Deviden Bulanan
            </a>
        </div>

        @if ($this->selectedLevel)
            <p style="color: #6b7280; font-size: 1rem;">
                Menampilkan daftar user pada level karir {{ $this->selectedLevel }}
            </p>
        @else
            <h1 style="font-size: 1.875rem; font-weight: 700; color: #111827; margin-bottom: 0rem;">
                Detail Mitra per Level Karir
            </h1>
            <p style="color: #6b7280; font-size: 1rem;">
                Pilih level karir untuk melihat detail user
            </p>
        @endif
    </div>

    <!-- Level Statistics -->
    @if (!empty($this->levelStats))
        <div
            style="background: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); padding: 1.5rem; margin-bottom: 0.5rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Statistik Level
                {{ $this->levelStats['nama_level'] }}</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div style="background: #eff6ff; padding: 1rem; border-radius: 0.5rem;">
                    <div style="font-size: 0.875rem; font-weight: 500; color: #2563eb;">Total Mitra</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #1e3a8a;">
                        {{ number_format($this->levelStats['jumlah_mitra'], 0, ',', '.') }}</div>
                </div>
                <div style="background: #f0fdf4; padding: 1rem; border-radius: 0.5rem;">
                    <div style="font-size: 0.875rem; font-weight: 500; color: #16a34a;">Memenuhi Syarat</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #15803d;">
                        {{ number_format($this->levelStats['jumlah_mitra_transaksi'], 0, ',', '.') }}</div>
                </div>
                <div style="background: #fefce8; padding: 1rem; border-radius: 0.5rem;">
                    <div style="font-size: 0.875rem; font-weight: 500; color: #ca8a04;">Minimal RO Bulanan</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #a16207;">
                        {{ $this->levelStats['minimal_ro_qr'] }}</div>
                </div>
            </div>
        </div>
    @endif

    <!-- Users Table -->
    @if (!empty($this->usersByLevel))
        <div
            style="background: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); padding: 1.5rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">
                Daftar User Level {{ $this->selectedLevel }}
            </h3>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #f9fafb;">
                        <tr>
                            <th
                                style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                                Level Karir</th>
                            <th
                                style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                                Nama</th>
                            <th
                                style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                                ID Mitra</th>
                            <th
                                style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                                RO Bulanan</th>
                            <th
                                style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                                Min RO</th>
                        </tr>
                    </thead>
                    <tbody style="background: white;">
                        @foreach ($this->usersByLevel as $user)
                            <tr style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.2s;"
                                onmouseover="this.style.backgroundColor='#f9fafb'"
                                onmouseout="this.style.backgroundColor='white'">
                                <td
                                    style="padding: 1rem; white-space: nowrap; font-size: 0.875rem; font-weight: 500; color: #111827;">
                                    {{ $user['plan_karir_sekarang'] }}
                                </td>
                                <td
                                    style="padding: 1rem; white-space: nowrap; font-size: 0.875rem; font-weight: 500; color: #111827;">
                                    {{ $user['nama'] }}
                                </td>
                                <td style="padding: 1rem; white-space: nowrap; font-size: 0.875rem; color: #6b7280;">
                                    {{ $user['id_mitra'] }}
                                </td>
                                <td style="padding: 1rem; white-space: nowrap; font-size: 0.875rem; color: #6b7280;">
                                    {{ number_format($user['jml_ro_bulanan'], 0, ',', '.') }}
                                </td>
                                <td style="padding: 1rem; white-space: nowrap; font-size: 0.875rem; color: #6b7280;">
                                    {{ $user['minimal_ro_bulanan'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Empty State -->
    @if (empty($this->usersByLevel) && $this->selectedLevel)
        <div
            style="background: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); padding: 3rem; text-align: center;">
            <div style="font-size: 1.125rem; font-weight: 600; color: #6b7280; margin-bottom: 0.5rem;">
                Tidak ada data user ditemukan
            </div>
            <div style="color: #9ca3af; font-size: 0.875rem;">
                Tidak ada user yang terdaftar pada level karir {{ $this->selectedLevel }}
            </div>
        </div>
    @endif

    <!-- No Level Selected State -->
    @if (!$this->selectedLevel)
        <div
            style="background: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); padding: 3rem; text-align: center;">
            <div style="font-size: 1.125rem; font-weight: 600; color: #6b7280; margin-bottom: 0.5rem;">
                Level Karir Belum Dipilih
            </div>
            <div style="color: #9ca3af; font-size: 0.875rem;">
                Silakan pilih level karir dari halaman Deviden Bulanan untuk melihat detail user
            </div>
        </div>
    @endif
</x-filament-panels::page>

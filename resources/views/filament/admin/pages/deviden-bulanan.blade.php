<x-filament-panels::page>
    <x-filament-panels::form>
        {{ $this->form }}
    </x-filament-panels::form>

    @if ($this->searchPerformed && $this->devidenBulananData)
        <div style="margin-top: 2rem; display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Action Buttons -->
            {{-- <div
                style="background: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); padding: 1.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Aksi</h3>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <button onclick="Livewire.dispatch('makeIncomeForUser')"
                        style="background: #059669; color: white; padding: 0.75rem 1.5rem; border-radius: 0.375rem; font-weight: 500; border: none; cursor: pointer; transition: background-color 0.2s;"
                        onmouseover="this.style.backgroundColor='#047857'"
                        onmouseout="this.style.backgroundColor='#059669'">
                        🎯 Distribusikan Penghasilan
                    </button>
                    <button onclick="Livewire.dispatch('processDevidenBulanan')"
                        style="background: #2563eb; color: white; padding: 0.75rem 1.5rem; border-radius: 0.375rem; font-weight: 500; border: none; cursor: pointer; transition: background-color 0.2s;"
                        onmouseover="this.style.backgroundColor='#1d4ed8'"
                        onmouseout="this.style.backgroundColor='#2563eb'">
                        💾 Simpan Data
                    </button>
                </div>
            </div> --}}

            <!-- Deviden Bulanan Summary Card -->
            <div
                style="background: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); padding: 1.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Ringkasan
                    Deviden
                    Bulanan</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                    <div style="background: #eff6ff; padding: 1rem; border-radius: 0.5rem;">
                        <div style="font-size: 0.875rem; font-weight: 500; color: #2563eb;">Omzet RO QR</div>
                        <div style="font-size: 1.125rem; font-weight: 600; color: #1e3a8a;">
                            Rp {{ number_format($this->devidenBulananData['omzet_ro_qr'], 0, ',', '.') }}</div>
                    </div>
                    <div style="background: #f0fdf4; padding: 1rem; border-radius: 0.5rem;">
                        <div style="font-size: 0.875rem; font-weight: 500; color: #16a34a;">Tanggal Awal</div>
                        <div style="font-size: 1.125rem; font-weight: 600; color: #15803d;">
                            {{ $this->devidenBulananData['start_date'] }}</div>
                    </div>
                    <div style="background: #fefce8; padding: 1rem; border-radius: 0.5rem;">
                        <div style="font-size: 0.875rem; font-weight: 500; color: #ca8a04;">Tanggal Akhir</div>
                        <div style="font-size: 1.125rem; font-weight: 600; color: #a16207;">
                            {{ $this->devidenBulananData['end_date'] }}</div>
                    </div>
                    <div style="background: #faf5ff; padding: 1rem; border-radius: 0.5rem;">
                        <div style="font-size: 0.875rem; font-weight: 500; color: #9333ea;">Tanggal Input</div>
                        <div style="font-size: 1.125rem; font-weight: 600; color: #7c3aed;">
                            {{ $this->devidenBulananData['tanggal_input'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Detail Deviden Bulanan Table -->
            @if (count($this->detailDevidenBulananData) > 0)
                <div
                    style="background: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); padding: 1.5rem;">
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Detail
                        Deviden Bulanan per Level Karir</h3>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead style="background: #f9fafb;">
                                <tr>
                                    <th
                                        style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                                        Level Karir
                                    </th>
                                    <th
                                        style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                                        Jumlah Mitra
                                    </th>
                                    <th
                                        style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                                        Jumlah Mitra Transaksi
                                    </th>
                                    <th
                                        style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                                        Omzet RO/QR
                                    </th>
                                    <th
                                        style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                                        Angka Deviden
                                    </th>
                                    <th
                                        style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                                        Nominal Deviden Bulanan
                                    </th>
                                </tr>
                            </thead>
                            <tbody style="background: white;">
                                @foreach ($this->detailDevidenBulananData as $detail)
                                    <tr style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.2s;"
                                        onmouseover="this.style.backgroundColor='#f9fafb'"
                                        onmouseout="this.style.backgroundColor='white'">
                                        <td
                                            style="padding: 1rem 1.5rem; white-space: nowrap; font-size: 0.875rem; font-weight: 500; color: #111827;">
                                            {{ $detail['nama_level'] }}
                                        </td>
                                        <td
                                            style="padding: 1rem 1.5rem; white-space: nowrap; font-size: 0.875rem; color: #6b7280;">
                                            {{ number_format($detail['jumlah_mitra'], 0, ',', '.') }}
                                        </td>
                                        <td
                                            style="padding: 1rem 1.5rem; white-space: nowrap; font-size: 0.875rem; color: #6b7280;">
                                            {{ number_format($detail['jumlah_mitra_transaksi'], 0, ',', '.') }}
                                        </td>
                                        <td
                                            style="padding: 1rem 1.5rem; white-space: nowrap; font-size: 0.875rem; color: #6b7280;">
                                            Rp {{ number_format($detail['omzet_ro_qr'], 0, ',', '.') }}
                                        </td>
                                        <td
                                            style="padding: 1rem 1.5rem; white-space: nowrap; font-size: 0.875rem; color: #6b7280;">
                                            {{ str_replace('%', '', $detail['angka_deviden']) }}
                                        </td>
                                        <td
                                            style="padding: 1rem 1.5rem; white-space: nowrap; font-size: 0.875rem; font-weight: 600; color: #16a34a;">
                                            Rp {{ number_format($detail['nominal_deviden_bulanan'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Summary Statistics -->
            @if (count($this->detailDevidenBulananData) > 0)
                <div
                    style="background: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); padding: 1.5rem;">
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Statistik
                        Ringkas</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                        <div style="background: #eef2ff; padding: 1rem; border-radius: 0.5rem;">
                            <div style="font-size: 0.875rem; font-weight: 500; color: #4f46e5;">Total Level Karir</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #3730a3;">
                                {{ count($this->detailDevidenBulananData) }}</div>
                        </div>
                        <div style="background: #ecfdf5; padding: 1rem; border-radius: 0.5rem;">
                            <div style="font-size: 0.875rem; font-weight: 500; color: #059669;">Total Mitra</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #047857;">
                                {{ number_format(collect($this->detailDevidenBulananData)->sum('jumlah_mitra'), 0, ',', '.') }}
                            </div>
                        </div>
                        <div style="background: #fef2f2; padding: 1rem; border-radius: 0.5rem;">
                            <div style="font-size: 0.875rem; font-weight: 500; color: #dc2626;">Total Mitra Transaksi
                            </div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: #b91c1c;">
                                {{ number_format(collect($this->detailDevidenBulananData)->sum('jumlah_mitra_transaksi'), 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
</x-filament-panels::page>

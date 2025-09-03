<x-filament-panels::page>
    <div
        style="background: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); padding: 1.5rem;">
        <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Daftar Mitra </h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f9fafb;">
                    <tr>
                        <th
                            style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                            No</th>
                        <th
                            style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                            Nama</th>
                        <th
                            style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                            ID Mitra</th>
                        <th
                            style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                            Poin Reward</th>
                        <th
                            style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                            Tanggal Daftar</th>
                    </tr>
                </thead>
                <tbody style="background: white;">
                    @forelse ($this->mitraList as $i => $mitra)
                        <tr style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.2s;"
                            onmouseover="this.style.backgroundColor='#f9fafb'"
                            onmouseout="this.style.backgroundColor='white'">
                            <td
                                style="padding: 1rem 1.5rem; white-space: nowrap; font-size: 0.875rem; font-weight: 500; color: #111827;">
                                {{ $i + 1 }}</td>
                            <td style="padding: 1rem 1.5rem; white-space: nowrap; font-size: 0.875rem; color: #6b7280;">
                                {{ $mitra->nama ?? '-' }}</td>
                            <td style="padding: 1rem 1.5rem; white-space: nowrap; font-size: 0.875rem; color: #6b7280;">
                                {{ $mitra->id_mitra ?? '-' }}</td>
                            <td style="padding: 1rem 1.5rem; white-space: nowrap; font-size: 0.875rem; color: #6b7280;">
                                {{ (int) ($mitra->poin_reward ?? 0) }}

                            </td>
                            <td style="padding: 1rem 1.5rem; white-space: nowrap; font-size: 0.875rem; color: #6b7280;">
                                {{ $mitra->tgl_daftar ? \Carbon\Carbon::parse($mitra->tgl_daftar)->format('d-m-Y') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5"
                                style="padding: 2rem 1.5rem; text-align: center; font-size: 0.875rem; color: #9ca3af;">
                                Tidak ada mitra dengan poin &ge; 20.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>

<x-filament-panels::page>
    <div
        style="background: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); padding: 1.5rem;">

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f9fafb;">
                    <tr>
                        <th
                            style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                            No
                        </th>
                        <th
                            style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                            Nama Pembeli
                        </th>
                        <th
                            style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                            Tanggal Beli
                        </th>
                        <th
                            style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                            Nama Produk
                        </th>
                        <th
                            style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                            Harga Beli
                        </th>
                        <th
                            style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">
                            Status Pembelian
                        </th>
                    </tr>
                </thead>
                <tbody style="background: white;">
                    @forelse ($this->pembelianDetails as $i => $detail)
                        <tr style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.2s;"
                            onmouseover="this.style.backgroundColor='#f9fafb'"
                            onmouseout="this.style.backgroundColor='white'">
                            <td
                                style="padding: 1rem 1.5rem; white-space: nowrap; font-size: 0.875rem; font-weight: 500; color: #111827;">
                                {{ $i + 1 }}
                            </td>
                            <td style="padding: 1rem 1.5rem; white-space: nowrap; font-size: 0.875rem; color: #6b7280;">
                                {{ $detail->pembelian->user->nama ?? '-' }}
                            </td>
                            <td style="padding: 1rem 1.5rem; white-space: nowrap; font-size: 0.875rem; color: #6b7280;">
                                {{ $detail->pembelian->tgl_beli ?? '-' }}
                            </td>
                            <td style="padding: 1rem 1.5rem; white-space: nowrap; font-size: 0.875rem; color: #6b7280;">
                                {{ $detail->nama_produk ?? '-' }}
                            </td>
                            <td
                                style="padding: 1rem 1.5rem; white-space: nowrap; font-size: 0.875rem; font-weight: 600; color: #16a34a;">
                                Rp {{ number_format($detail->harga_beli, 0, ',', '.') }}
                            </td>
                            <td style="padding: 1rem 1.5rem; white-space: nowrap; font-size: 0.875rem; color: #6b7280;">
                                {{ $detail->pembelian->status_pembelian ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"
                                style="padding: 2rem 1.5rem; text-align: center; font-size: 0.875rem; color: #9ca3af;">
                                Tidak ada data detail pembelian untuk tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>

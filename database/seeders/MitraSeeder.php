<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MitraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load provinces
        $provinces = [];
        if (($handle = fopen(public_path('data/provinces.csv'), 'r')) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                $provinces[$data[0]] = $data[1];
            }
            fclose($handle);
        }

        // Load regencies
        $regencies = [];
        if (($handle = fopen(public_path('data/regencies.csv'), 'r')) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                $regencies[] = [
                    'id' => $data[0],
                    'province_id' => $data[1],
                    'name' => $data[2],
                ];
            }
            fclose($handle);
        }

        $createdUsers = [];
        for ($i = 0; $i < 15; $i++) {
            $urutan = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
            $today = '20250723';
            $idMitra = 'G' . $today . $urutan;
            $username = 'mitra' . ($i + 2);
            $email = $username . '@gmail.com';
            [$provinsi, $kabupaten] = $this->getFirstProvinceAndRegency($provinces, $regencies);
            $groupSponsor = array_column(array_slice($createdUsers, max(0, $i - 9), 9), 'id');

            $idSponsor = $i > 0 ? $createdUsers[$i - 1]['id'] : null;

            $statusQR = $i < 2 ? false : true;

            $user = User::create([
                'id_mitra' => $idMitra,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make('password'),
                'isAdmin' => false,
                'isStockis' => false,
                'status_qr' => $statusQR,
                'id_sponsor' => $idSponsor,
                'nama' => 'Mitra Glowink ' . ($i + 2),
                'provinsi' => $provinsi,
                'kabupaten' => $kabupaten,
                'alamat' => 'Jl. Mitra No. ' . ($i + 2),
                'no_telp' => '0812345678' . str_pad($i + 2, 2, '0', STR_PAD_LEFT),
                'no_rek' => '12345678' . str_pad($i + 2, 2, '0', STR_PAD_LEFT),
                'nama_rekening' => 'Mitra Glowink ' . ($i + 2),
                'bank' => 'BNI',
                'tgl_daftar' => now(),
                'group_sponsor' => $groupSponsor,
                'saldo_penghasilan' => 0,
                'poin_reward' => in_array($i, [2, 6]) ? 25 : 0,
                'plan_karir_sekarang' => 'bronze',
                'next_plan_karir' => 'bronze',
                'next_poin_karir' => 0,
            ]);

            $createdUsers[] = [
                'id' => $user->id,
                'id_mitra' => $idMitra,
            ];

            // --- DUMMY PEMBELIAN UNTUK MITRA ---
            // 1. Aktivasi Member
            $this->createDummyPembelian($user, 'aktivasi member');
            // 2. Repeat Order
            $this->createDummyPembelian($user, 'repeat order');
        }
    }

    private function getFirstProvinceAndRegency($provinces, $regencies)
    {
        reset($provinces);
        $provinceId = key($provinces);
        $provinceName = current($provinces);
        $filteredRegencies = array_filter($regencies, fn($regency) => $regency['province_id'] == $provinceId);
        $regency = $filteredRegencies ? reset($filteredRegencies) : null;
        return [$provinceName, $regency ? $regency['name'] : null];
    }

    private function createDummyPembelian($user, $kategori)
    {
        $status = 'selesai';
        $adminUser = \App\Models\User::where('isAdmin', true)->first();
        $stockist = \App\Models\User::where('isStockis', true)->inRandomOrder()->first();
        $buyer = $user;
        $seller = $kategori === 'aktivasi member' ? ($stockist ?? $adminUser) : ($stockist ?? $adminUser);
        $paket = $kategori === 'aktivasi member' ? 1 : 2;
        $produkList = \App\Models\Produk::where('paket', $paket)->get();
        if ($produkList->isEmpty()) {
            return;
        }

        $produk = $produkList->random();
        $qty = rand(1, 3);
        $harga = $produk->harga_member;
        $total = $qty * $harga;
        $tglBeli = ($kategori === 'aktivasi member') ? now()->format('Y-m-d') : now()->subDays(rand(1, 30))->format('Y-m-d');
        $pembelian = \App\Models\Pembelian::create([
            'tgl_beli' => $tglBeli,
            'user_id' => $buyer->id,
            'beli_dari' => $seller->id,
            'tujuan_beli' => 'null',
            'nama_penerima' => $buyer->nama,
            'no_telp' => $buyer->no_telp ?? '081234567890',
            'alamat_tujuan' => $buyer->alamat ?? 'Alamat Member',
            'total_beli' => $total,
            'total_bonus' => 0,
            'status_pembelian' => $status,
            'jumlah_poin_qr' => 0,
            'kategori_pembelian' => $kategori,
        ]);
        \App\Models\PembelianDetail::create([
            'pembelian_id' => $pembelian->id,
            'produk_id' => $produk->id,
            'nama_produk' => $produk->nama,
            'paket' => $produk->paket,
            'jml_beli' => $qty,
            'harga_beli' => $harga,
            'nominal_bonus_sponsor' => 0,
            'nominal_bonus_generasi' => 0,
            'user_id_get_bonus_sponsor' => null,
            'group_user_id_get_bonus_generasi' => null,
        ]);
    }
}

<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateOldToUsers extends Command
{
    protected $signature = 'migrate:old-users';
    protected $description = 'Migrasi data dari DB lama ke tabel users baru';

    public function handle()
    {
        $this->info('Memulai migrasi users dari database lama...');

        DB::transaction(function () {
            // Step 1: Ambil data uplink untuk mapping
            $uplinks = DB::connection('mysql_old')
                ->table('user')
                ->select('username', 'uplink')
                ->get()
                ->keyBy('username');

            // Step 2: Insert users tanpa id_sponsor dulu
            $oldUsers = DB::connection('mysql_old')
                ->table('user as u')
                ->leftJoin('alamat as a', 'u.username', '=', 'a.username')
                ->leftJoin('rekening as r', 'u.username', '=', 'r.username')
                ->leftJoin('stokis as s', 'u.username', '=', 's.username')
                ->leftJoin('penghasilan as p', 'u.username', '=', 'p.username')
                ->select(
                    'u.username',
                    'u.nama',
                    'a.alamatlengkap',
                    'u.pendaftaran',
                    'u.nohp',
                    'u.password',
                    's.kota',
                    's.provinsi',
                    'r.bank',
                    'r.nama as nama_rekening',
                    'r.rekening as no_rek',
                    'p.point',
                    'p.qr',
                    's.username as stokis_username',
                    'u.uplink'
                )
                ->get();

            $mapping = []; // username → id baru
            $skipped = 0;

            foreach ($oldUsers as $old) {
                // Cek apakah user dengan id_mitra ini sudah ada
                $existingUser = User::where('id_mitra', $old->username)->first();

                if ($existingUser) {
                    $this->warn("User dengan id_mitra '{$old->username}' sudah ada, dilewati...");
                    $mapping[$old->username] = $existingUser->id;
                    $skipped++;
                    continue;
                }

                // Disable UserObserver sementara untuk menghindari auto-generate id_mitra
                User::unsetEventDispatcher();

                // Tentukan level karir berdasarkan poin
                $poin = $old->point ?? 0;
                $planKarirSekarang = $this->determineCareerLevel($poin);

                // Tentukan id_sponsor jika uplink ada
                $idSponsor = null;
                if ($old->uplink && isset($mapping[$old->uplink])) {
                    $idSponsor = $mapping[$old->uplink];
                }

                $newUser = User::create([
                    'id_mitra' => substr($old->username, 0, 255), // username → id_mitra
                    'nama' => substr($old->nama ?? '', 0, 255),
                    'alamat' => substr($old->alamatlengkap ?? '', 0, 255), // alamatlengkap → alamat
                    'created_at' => $old->pendaftaran, // pendaftaran → created_at
                    'no_telp' => substr($old->nohp ?? '', 0, 255), // nohp → no_telp
                    'password' => $old->password ?? bcrypt('password'),
                    'kabupaten' => substr($old->kota ?? '', 0, 255), // kota → kabupaten
                    'provinsi' => substr($old->provinsi ?? '', 0, 255),
                    'bank' => substr($old->bank ?? '', 0, 255),
                    'nama_rekening' => substr($old->nama_rekening ?? '', 0, 255),
                    'no_rek' => substr($old->no_rek ?? '', 0, 255), // rekening → no_rek
                    'poin_reward' => $poin, // point → poin_reward
                    'plan_karir_sekarang' => $planKarirSekarang,
                    'status_qr' => $old->qr == 1 ? true : false, // qr → status_qr (boolean)
                    'isStockis' => $old->stokis_username ? 1 : 0, // stokis username → isStockis
                    'id_sponsor' => $idSponsor, // uplink → id_sponsor (mapping ke ID)
                ]);

                $mapping[$old->username] = $newUser->id;

                // Re-enable UserObserver
                User::setEventDispatcher(app('events'));
            }

            $this->info('Migrasi selesai! Total user: ' . count($mapping) . ', Dilewati: ' . $skipped);
        });
    }

    /**
     * Tentukan level karir berdasarkan poin reward
     * Menggunakan aturan yang sama dengan ChangeLevelUserListener
     */
    private function determineCareerLevel($poin)
    {
        // Daftar level dan ketentuannya (urutan dari terendah ke tertinggi)
        $levels = [
            'bronze' => ['poin' => 20, 'bonus' => 100000],
            'silver' => ['poin' => 100, 'bonus' => 400000],
            'gold' => ['poin' => 750, 'bonus' => 2500000],
            'platinum' => ['poin' => 3000, 'bonus' => 10000000],
            'titanium' => ['poin' => 15000, 'bonus' => 50000000],
            'ambassador' => ['poin' => 60000, 'bonus' => 200000000],
            'chairman' => ['poin' => 150000, 'bonus' => 500000000],
        ];

        $levelNames = array_keys($levels);
        $highestLevel = null;

        // Cari level tertinggi yang dicapai berdasarkan poin
        foreach ($levels as $levelName => $data) {
            if ($poin >= $data['poin']) {
                $highestLevel = $levelName;
            }
        }

        return $highestLevel; // Return null jika belum mencapai level apapun
    }
}

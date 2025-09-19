<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Produk;
use App\Models\ProdukStok;
use App\Models\JaringanMitra;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;
use Spatie\LaravelIgnition\Recorders\DumpRecorder\Dump;

class MigrateOldToUsers extends Command
{
    protected $signature = 'migrate:old-users';
    protected $description = 'Migrasi data dari DB lama ke tabel users baru';

    public function handle()
    {
        // $this->migrateTableUser();
        // $this->migrateTableUp();
        // User::query()->update(['password' => bcrypt('password')]);
        // $this->setJaringan();
        $this->cekStokisAndStok();

    }

    public function cekStokisAndStok(){
            $stokis = DB::connection('mysql_old')
            ->table('stokis')
            ->select('username')
            ->pluck('username');

            User::whereIn('username' , $stokis)->update(['isStockis' => 1]);
            $this->info('Proses update stokis selesai ...');

    }

    public function cekPoin(){
         $poin = $old->point ?? 0;
        $planKarirSekarang = $this->determineCareerLevel($poin);
    }

    public function migrateTableUp(){
        $this->info('Proses migrasi  dari table up ...');
            DB::transaction(function(){
                $old_user = DB::connection('mysql_old')
                            ->table('up as u')
                            ->select(
                                'u.id_mem as id_mitra',
                                'u.id_mem as username',
                                DB::raw("CONCAT(u.id_mem , '@glowink.net') as email"),
                                'u.id_sponsor as id_sponsor_old',
                            )
                            ->where('u.id_mem' ,'NOT LIKE' , '%-%')
                            ->whereNotIn('u.id_mem' , function ($query){
                                $query->select('username')->from('user');
                            })
                            ->orderBy('u.id' ,'ASC')
                            ->groupBy('u.id_mem')
                            ->get();

                    $new_user =  collect($old_user)->map(function($user){
                        return (array)$user;
                    })->toArray() ;


                    User::insert((array)$new_user);

            });
            $this->info('Proses migrasi dari table up selesai ...');

    }

     public function migrateTableUser(){
        $this->info('Proses migrasi dari table user ...');
        DB::transaction(function(){
            $old_user = DB::connection('mysql_old')
                        ->table('user as u')
                        ->leftJoin('alamat as a', 'u.username', '=', 'a.username')
                        ->leftJoin('rekening as r', 'u.username', '=', 'r.username')
                        ->leftJoin('stokis as s', 'u.username', '=', 's.username')
                        ->leftJoin('penghasilan as p', 'u.username', '=', 'p.username')
                        ->select(
                            'u.username as id_mitra',
                            'u.username as username',
                            DB::raw("CONCAT(u.username , '@glowink.net') as email"),
                            'u.nama as nama',
                            'a.alamatlengkap as alamat',
                            'u.pendaftaran as tgl_daftar',
                            'u.nohp as no_telp',
                            'u.password as password',
                            's.kota as kabupaten',
                            's.provinsi as provinsi',
                            'r.bank as bank',
                            'r.nama as nama_rekening',
                            'r.rekening as no_rek',
                            'u.uplink as id_sponsor_old',
                            'p.penghasilan as saldo_penghasilan',
                            'p.penghasilan as saldo_withdraw',
                            'p.point as poin_reward',
                            'p.qr as status_qr',
                        )
                        ->where('u.username' ,'NOT LIKE' , '%-%')
                        ->orderBy('u.id' ,'ASC')
                        ->groupBy('u.username')
                        ->get();

                 $new_user =  collect($old_user)->map(function($user){
                        $poin = $user->poin_reward ?? 0;
                        $planKarirSekarang = $this->determineCareerLevel($poin);
                        $userObj = (object)$user;
                        $userObj->plan_karir_sekarang = $planKarirSekarang;
                    return (array)$userObj;
                 })->toArray() ;



                User::insert($new_user);
        });
        $this->info('Proses migrasi dari table user  selesai...');
    }

    public function setJaringan(){
         $this->info('set jaringan di table jaringan mitra...');
         $this->info('Proses update id sponsor dengan id system baru ...');


         User::all()->map(function ($data){

                $id_sponsor_old = DB::connection('mysql_old')->table('up')->where('id_mem' , $data->username)->value('id_sponsor');
                $id_sponsor_new =  User::where('id_mitra' , $id_sponsor_old)->value('id');

                $data->update(['id_sponsor' => $id_sponsor_new]);
            });

            //level 1
            $this->info('Proses set jaringan level 1');
            $users       = User::all();
            $count_data  = count($users);
            $data_insert = [];
            $j           = 0;

            foreach ($users as $mitra) {
                if ($mitra->id_sponsor !== null) {
                    $data_insert[] = [
                        'user_id'=>$mitra->id ,
                        'sponsor_id'=>$mitra->id_sponsor ,
                        'level' =>1,
                    ];
                }
                $j++;
                 $this->info("Proses set jaringan level 1 dengan jumlah data $count_data, user ke $j  dari total $count_data ");
            }
            $this->info("Proses set jaringan level 1  ke database ");
            JaringanMitra::insertOrIgnore($data_insert);
            $this->info("Proses set jaringan level 1 Selesai ");

            //generate table jaringan dari 2 dst
            for ($k=2; $k <  100; $k++) {
             //mencari data level 2 dst


                $data = JaringanMitra::where('level' , $k-1)->orderBy('sponsor_id')->get();
                $x = 0;
                foreach ($data as $mitra) {
                    $x++;
                    $y=0;
                    //get data yang akan dimasukan ke dalam level selanjutnya
                    $data_level = JaringanMitra::where('level' , 1)->where('sponsor_id' , $mitra->user_id)->get();
                    foreach ($data_level as $new_mitra) {
                        $y++;

                            $id_sponsor = $new_mitra->sponsor_id;
                            for ($i=1; $i < $k ; $i++) {
                                $id_sponsor = User::where('id', $id_sponsor)->value('id_sponsor');

                            }


                            if (!jaringanMitra::where('user_id' , $new_mitra->user_id)->where('sponsor_id' , $id_sponsor)->exists()) {
                                if ($id_sponsor !== null) {
                                        $data_insert = [
                                            'user_id'    => $new_mitra->user_id,
                                            'sponsor_id' => $id_sponsor,
                                            'level'      => $k,
                                        ];

                                        JaringanMitra::create($data_insert);
                                }
                                $jml_data       = count($data);
                                $jml_data_level = count($data_level);
                                $this->info("Proses jaringan level $k dengan jml data $jml_data ( user ke $x dari $jml_data dengan jml sub user $jml_data_level sekarang urutan ke $y )");
                            }

                    }
                }
            }

            $this->info('Proses migrasi selesai');

    }



    public function migrateFromUsers(){
        $this->info('Memulai migrasi users dari database lama...');

        DB::transaction(function () {
            // Step 1: Ambil data uplink untuk mapping
            // $uplinks = DB::connection('mysql_old')
            //     ->table('user')
            //     ->select('username', 'uplink')
            //     ->get()
            //     ->keyBy('username');

            // Step 2: Insert users tanpa id_sponsor dulu
            $oldUsers = DB::connection('mysql_old')
                ->table('user as u')
                ->leftJoin('alamat as a', 'u.username', '=', 'a.username')
                ->leftJoin('rekening as r', 'u.username', '=', 'r.username')
                ->leftJoin('stokis as s', 'u.username', '=', 's.username')
                ->leftJoin('penghasilan as p', 'u.username', '=', 'p.username')
                // ->leftJoin('up as up', 'u.username', '=', 'up.id_mem')
                ->leftJoin('bonuspending as bp', 'u.username', '=', 'bp.username')

                ->select(
                    'u.id',
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
                    'up.id_sponsor as uplink',
                    'bp.bonuspending'
                )
                ->orderBy('u.id' ,'ASC')
                ->get();

            $mapping = []; // username → id baru
            $skipped = 0;
            // foreach ($oldUsers as $data) {
            //     echo $data->id . ' # '. $data->username .' # '. $data->nama;
            // }
            // dd('ok');

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
                // $idSponsor = null;
                // if ($old->uplink && isset($mapping[$old->uplink])) {
                //     $idSponsor = $mapping[$old->uplink];
                // }

                $newUser = User::create([
                    'id' =>$old->id,
                    'id_mitra' => substr($old->username, 0, 255), // username → id_mitra
                    'username' => substr($old->username, 0, 255), // username → id_mitra
                    'email' => substr($old->username, 0, 255).'@glowink.net', // username → id_mitra
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
                    'saldo_penghasilan'=>$old->bonuspending ?? 0
                    // 'id_sponsor' => $old->uplink, // uplink → id_sponsor (mapping ke ID)
                ]);

                $mapping[$old->username] = $newUser->id;

                // Re-enable UserObserver
                User::setEventDispatcher(app('events'));
            }

            User::all()->map(function ($data){
                $id_sponsor_old = DB::connection('mysql_old')->table('up')->where('id_mem' , $data->username)->value('id_sponsor');
                $id_sponsor_new =  User::where('id_mitra' , $id_sponsor_old)->value('id');

                $data->update(['id_sponsor' => $id_sponsor_new]);
            });

            //level 1
            $users = User::all();
            foreach ($users as $mitra) {
                if ($mitra->id_sponsor !== null) {
                    $data_insert = [
                        'user_id'=>$mitra->id ,
                        'sponsor_id'=>$mitra->id_sponsor ,
                        'level' =>1,
                    ];
                    JaringanMitra::create($data_insert);
                }
            }

            // level 2
           $data = JaringanMitra::where('level' ,1)->orderBy('sponsor_id')->get();
           foreach ($data as $mitra) {
                $id_sponsor_level1 = $mitra->sponsor_id;
                $data_level2= JaringanMitra::where('sponsor_id' , $mitra->user_id)->get();
                foreach ($data_level2  as $mitra2) {
                    if ($mitra2->sponsor_id !== null ) {
                        # code...
                        $data_insert = [
                            'user_id'=>$mitra2->user_id ,
                            'sponsor_id'=>$id_sponsor_level1 ,
                            'level' =>2,
                        ];
                        JaringanMitra::create($data_insert);
                    }
                }
           }

          for ($k=3; $k <  30; $k++) {
             //level 3
                $data = JaringanMitra::where('level' , $k-1)->orderBy('sponsor_id')->get();
                foreach ($data as $mitra) {
                        //get data yang akan dimasukan ke dalam level 3
                        $data_level = JaringanMitra::where('level' , 1)->where('sponsor_id' , $mitra->user_id)->get();
                    foreach ($data_level as $new_mitra) {

                        //yang jadi sponsor sekarang sebgai level 3
                            $id_sponsor = $new_mitra->sponsor_id;
                            for ($i=1; $i < $k+1 ; $i++) {
                                $id_sponsor = User::where('id', $id_sponsor)->value('id_sponsor');

                            }


                            if (!jaringanMitra::where('user_id' , $new_mitra->user_id)->where('sponsor_id' , $id_sponsor)->exists()) {
                                    if ($id_sponsor !== null) {
                                    $data_insert = [
                                        'user_id'    => $new_mitra->user_id,
                                        'sponsor_id' => $id_sponsor,
                                        'level'      => $k,
                                    ];
                                    JaringanMitra::create($data_insert);
                            }
                            }

                    }
                }
            }

            //update stok produk
            $data_stok = DB::connection('mysql_old')->table('stokis_produk')->where('stok' , '>' , 0)->get();
            foreach ($data_stok as $data) {
                $produk_id = Produk::where('nama' , $data->nama_produk)->value('id');
                $user_id = User::where('username' , $data->stokis)->value('id');
                $data_stok =[
                    'produk_id' => $produk_id,
                    'user_id'   => $user_id,
                    'stok'      => $data->stok
                ];
                ProdukStok::create($data_stok);
            }


            $this->info('Migrasi selesai!');
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

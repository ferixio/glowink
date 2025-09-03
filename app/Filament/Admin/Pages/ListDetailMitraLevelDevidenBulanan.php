<?php

namespace App\Filament\Admin\Pages;

use App\Models\LevelKarir;
use App\Models\User;
use Filament\Pages\Page;

class ListDetailMitraLevelDevidenBulanan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.admin.pages.list-detail-mitra-level-deviden-bulanan';
    protected static ?int $navigationSort = 3;
    protected static bool $shouldRegisterNavigation = false;

    public $usersByLevel = [];
    public $selectedLevel = '';
    public $levelStats = [];

    public function mount()
    {
        // Get level parameter from URL
        $this->selectedLevel = request()->query('level', '');

        if ($this->selectedLevel) {
            $this->loadUsersByLevel($this->selectedLevel);
            $this->loadLevelStats($this->selectedLevel);
        }
    }

    private function loadUsersByLevel(string $levelName): void
    {
        $level = LevelKarir::where('nama_level', $levelName)->first();
        if (!$level) {
            return;
        }

        $minimalROQR = $level->minimal_RO_QR;

        $users = User::where('plan_karir_sekarang', $levelName)
            ->select([
                'id', 'nama', 'id_mitra',
                'jml_ro_bulanan', 'minimal_ro_bulanan',
                'plan_karir_sekarang',
            ])
            ->orderBy('nama')
            ->get();

        $this->usersByLevel = $users->map(function ($user) use ($minimalROQR) {
            return [
                'id' => $user->id,
                'nama' => $user->nama,
                'id_mitra' => $user->id_mitra,
                'jml_ro_bulanan' => $user->jml_ro_bulanan,
                'plan_karir_sekarang' => $user->plan_karir_sekarang,
                'memenuhi_syarat' => $user->jml_ro_bulanan >= $minimalROQR,
                'minimal_ro_bulanan' => $minimalROQR,
            ];
        })->toArray();
    }

    private function loadLevelStats(string $levelName): void
    {
        $level = LevelKarir::where('nama_level', $levelName)->first();
        if (!$level) {
            return;
        }

        $minimalROQR = $level->minimal_RO_QR;
        $jumlahMitra = User::where('plan_karir_sekarang', $levelName)->count();
        $jumlahMitraTransaksi = User::where('plan_karir_sekarang', $levelName)
            ->where('jml_ro_bulanan', '>=', $minimalROQR)
            ->count();

        $this->levelStats = [
            'nama_level' => $levelName,
            'minimal_ro_qr' => $minimalROQR,
            'jumlah_mitra' => $jumlahMitra,
            'jumlah_mitra_transaksi' => $jumlahMitraTransaksi,
        ];
    }
}

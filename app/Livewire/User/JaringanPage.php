<?php

namespace App\Livewire\User;

use App\Models\JaringanMitra;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class JaringanPage extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedLevel = '';
    public $perPage = 10;
    public $showLevelFilter = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedLevel' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedLevel()
    {
        $this->resetPage();
    }

    public function filterByLevel($level)
    {
        $this->selectedLevel = $level;
        $this->showLevelFilter = true;
        $this->resetPage();
    }

    public function clearLevelFilter()
    {
        $this->selectedLevel = '';
        $this->showLevelFilter = false;
        $this->resetPage();
    }

    public function render()
    {
        $currentUser = Auth::user();

        // Ambil data jaringan mitra untuk user yang sedang login dengan eager loading sponsor
        $query = JaringanMitra::with(['user.sponsorWithMitra'])
            ->where('sponsor_id', $currentUser->id);

        // Filter berdasarkan level
        if ($this->selectedLevel) {
            $query->where('level', $this->selectedLevel);
        }

        // Filter berdasarkan nama user atau sponsor
        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $jaringanMitra = $query->orderBy('level')
            ->orderBy('created_at')
            ->paginate($this->perPage);

        // Statistik per level
        $levelStats = JaringanMitra::where('sponsor_id', $currentUser->id)
            ->selectRaw('level, COUNT(*) as total')
            ->groupBy('level')
            ->orderBy('level')
            ->get();

        // Total downline
        $totalDownline = JaringanMitra::where('sponsor_id', $currentUser->id)->count();

        // Level yang tersedia untuk filter
        $availableLevels = JaringanMitra::where('sponsor_id', $currentUser->id)
            ->select('level')
            ->distinct()
            ->orderBy('level')
            ->pluck('level');

        return view('livewire.user.jaringan-page', [
            'jaringanMitra' => $jaringanMitra,
            'levelStats' => $levelStats,
            'totalDownline' => $totalDownline,
            'availableLevels' => $availableLevels,
        ]);
    }
}

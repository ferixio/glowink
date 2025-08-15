<?php

namespace App\Livewire\User;

use App\Models\Penghasilan;
use App\Models\Withdraw;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class WithdrawPage extends Component
{
    public $nominal_withdraw;
    public $showWithdrawForm = false;

    public function toggleWithdrawForm()
    {
        $this->showWithdrawForm = !$this->showWithdrawForm;
    }

    public function updatedNominalWithdraw($value)
    {
        // Ambil hanya angka dari input (misal: 'Rp 100.000' jadi 100000)
        $this->nominal_withdraw = preg_replace('/[^\d]/', '', $value);
    }
    public $user;

    public function mount()
    {
        $this->user = Auth::user();

    }

    public function createWithdraw()
    {
        // Pastikan nominal_withdraw hanya angka
        $nominal = preg_replace('/[^\d]/', '', $this->nominal_withdraw);
        $this->nominal_withdraw = $nominal;
        $this->validate([
            'nominal_withdraw' => 'required|numeric|min:10000|max:' . $this->user->saldo_penghasilan,
        ]);

        // Check if user has sufficient balance
        if ($this->nominal_withdraw > $this->user->saldo_penghasilan) {
            Notification::make()
                ->title('Saldo tidak mencukupi')
                ->body('Saldo penghasilan Anda tidak mencukupi untuk melakukan withdraw')
                ->danger()
                ->send();
            return;
        }

        // Check if user has bank account info
        if (empty($this->user->no_rek) || empty($this->user->nama_rekening) || empty($this->user->bank)) {
            Notification::make()
                ->title('Informasi bank tidak lengkap')
                ->body('Silakan lengkapi informasi rekening bank Anda terlebih dahulu')
                ->warning()
                ->send();
            return;
        }

        try {
            // Create withdraw record using original table structure
            Withdraw::create([
                'user_id' => $this->user->id,
                'tgl_withdraw' => now()->toDateString(),
                'nominal' => $this->nominal_withdraw,
                'status' => 'pending',
            ]);

            // Update user's saldo_penghasilan dan saldo_withdraw
            $this->user->update([
                'saldo_penghasilan' => $this->user->saldo_penghasilan - $this->nominal_withdraw,
                'saldo_withdraw' => $this->user->saldo_withdraw + $this->nominal_withdraw,
            ]);

            // Reset form
            $this->nominal_withdraw = '';

            Notification::make()
                ->title('Withdraw berhasil dibuat')
                ->body('Permintaan withdraw Anda telah berhasil dibuat dan sedang menunggu approval')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Terjadi kesalahan')
                ->body('Gagal membuat permintaan withdraw. Silakan coba lagi.')
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        $userId = Auth::id();

        // Limit list data and select only needed columns
        $penghasilanList = Penghasilan::where('user_id', $userId)
            ->orderBy('tgl_dapat_bonus', 'desc')
            ->select(['tgl_dapat_bonus', 'keterangan', 'kategori_bonus', 'nominal_bonus'])
            ->limit(50)
            ->get();

        $withdrawHistory = Withdraw::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->select(['tgl_withdraw', 'nominal', 'status'])
            ->limit(50)
            ->get();

        // Aggregated totals without loading all rows into memory
        $totalsByCategory = Penghasilan::where('user_id', $userId)
            ->whereIn('kategori_bonus', ['Bonus Sponsor', 'Bonus Generasi', 'deviden harian', 'deviden bulanan'])
            ->selectRaw('kategori_bonus, SUM(nominal_bonus) as total')
            ->groupBy('kategori_bonus')
            ->pluck('total', 'kategori_bonus');

        $totalSponsor = (float) ($totalsByCategory['Bonus Sponsor'] ?? 0);
        $totalGenerasi = (float) ($totalsByCategory['Bonus Generasi'] ?? 0);
        $totalDividen = (float) (($totalsByCategory['deviden harian'] ?? 0) + ($totalsByCategory['deviden bulanan'] ?? 0));
        $totalPenghasilan = (float) Penghasilan::where('user_id', $userId)->sum('nominal_bonus');
        $totalWithdraw = (float) Withdraw::where('user_id', $userId)->sum('nominal');

        return view('livewire.user.withdraw-page', [
            'penghasilanList' => $penghasilanList,
            'withdrawHistory' => $withdrawHistory,
            'totalSponsor' => $totalSponsor,
            'totalDividen' => $totalDividen,
            'totalGenerasi' => $totalGenerasi,
            'totalPenghasilan' => $totalPenghasilan,
            'totalWithdraw' => $totalWithdraw,
        ]);
    }
}

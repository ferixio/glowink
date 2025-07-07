<?php

namespace App\Livewire\User;

use App\Models\Pembelian;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadBuktiTransfer extends Component
{
    use WithFileUploads;

    public Pembelian $pembelian;
    public $bukti_transfer = [];
    public $isApprovePage = false;
    public $showModal = false;
    public $modalImageUrl = '';

    protected $rules = [
        'bukti_transfer.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:20480',
    ];

    public function showImageModal($imageUrl)
    {
        $this->modalImageUrl = $imageUrl;
        $this->showModal = true;
    }

    public function hideImageModal()
    {
        $this->showModal = false;
        $this->modalImageUrl = '';
    }

    public function uploadBuktiTransfer()
    {
        $this->validate();
        $images = $this->pembelian->images ?? [];
        foreach ($this->bukti_transfer as $file) {
            $path = $file->store('bukti_transfer', 'public');
            $images[] = $path;
        }
        $this->pembelian->images = $images;
        $this->pembelian->save();
        $this->bukti_transfer = [];
        session()->flash('success', 'Bukti transfer berhasil diupload.');
        $this->dispatch('buktiTransferUploaded');
    }

    public function removePreview($index)
    {
        if (isset($this->bukti_transfer[$index])) {
            array_splice($this->bukti_transfer, $index, 1);
        }
    }

    public function removeUploaded($index)
    {
        $images = $this->pembelian->images ?? [];
        if (isset($images[$index])) {
            // Hapus file dari storage
            Storage::disk('public')->delete($images[$index]);
            array_splice($images, $index, 1);
            $this->pembelian->images = $images;
            $this->pembelian->save();
        }
    }

    public function render()
    {
        return view('livewire.user.upload-bukti-transfer');
    }
}

<?php

namespace App\Livewire\User;

use App\Models\Pembelian;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
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
            // Resize gambar sebelum disimpan
            $resizedImage = $this->resizeImage($file);

            // Simpan gambar yang sudah diresize
            $path = $resizedImage->store('bukti_transfer', 'public');
            $images[] = $path;
        }

        $this->pembelian->images = $images;
        $this->pembelian->save();
        $this->bukti_transfer = [];
        session()->flash('success', 'Bukti transfer berhasil diupload.');
        $this->dispatch('buktiTransferUploaded');
    }

    private function resizeImage($file)
    {
        // Buat ImageManager dengan GD driver
        $manager = new ImageManager(new Driver());

        // Baca file gambar
        $image = $manager->read($file->getRealPath());

        // Dapatkan dimensi asli
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        // Tentukan ukuran maksimum yang diinginkan
        $maxWidth = 1200;
        $maxHeight = 1200;

        // Hitung rasio untuk mempertahankan aspect ratio
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);

        // Jika gambar lebih kecil dari maksimum, tidak perlu diresize
        if ($ratio >= 1) {
            return $file;
        }

        // Resize gambar
        $newWidth = (int) ($originalWidth * $ratio);
        $newHeight = (int) ($originalHeight * $ratio);

        $image->resize($newWidth, $newHeight);

        // Optimasi kualitas untuk mengurangi ukuran file
        $image->toJpeg(85); // 85% quality untuk keseimbangan ukuran dan kualitas

        // Buat temporary file untuk disimpan
        $tempPath = tempnam(sys_get_temp_dir(), 'resized_');
        file_put_contents($tempPath, $image->encode());

        // Buat UploadedFile dari temporary file
        return new \Illuminate\Http\UploadedFile(
            $tempPath,
            $file->getClientOriginalName(),
            'image/jpeg', // Set mime type ke JPEG karena kita encode ke JPEG
            null,
            true
        );
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

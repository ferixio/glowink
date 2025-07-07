<div class="pt-4">
    <p class="font-semibold text-sm mb-2">{{ $isApprovePage ? 'Lihat Bukti Transfer' : 'Upload Bukti Transfer' }}</p>
    {{-- Preview gambar yang baru dipilih (belum diupload) --}}
    @if (!empty($bukti_transfer))
        <div class="mt-2 text-xs text-gray-500">Preview gambar yang akan diupload:</div>
        <div class="flex flex-wrap gap-2 mt-2">
            @foreach ($bukti_transfer as $i => $preview)
                <div class="relative group">
                    <div class="relative">
                        <img src="{{ $preview->temporaryUrl() }}"
                            class="w-24 h-24 object-cover rounded border opacity-70 cursor-pointer hover:opacity-100 transition-all duration-200 hover:scale-105"
                            onclick="Livewire.find('{{ $this->getId() }}').call('showImageModal', '{{ $preview->temporaryUrl() }}')" />
                        <div
                            class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded flex items-center justify-center pointer-events-none">
                            <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                            </svg>
                        </div>
                    </div>
                    <button type="button" wire:click="removePreview({{ $i }})"
                        class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs opacity-80 group-hover:opacity-100 z-10">&times;</button>
                </div>
            @endforeach
        </div>
    @endif

    @if (!$isApprovePage)
        <form wire:submit.prevent="uploadBuktiTransfer" enctype="multipart/form-data" class="space-y-4 mt-4">
            <label for="bukti_transfer"
                class="flex items-center justify-center w-full px-4 py-6 bg-white text-yellow-600 rounded-lg border-2 border-dashed border-yellow-500 cursor-pointer hover:bg-yellow-50 transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 12v6m0 0l-3-3m3 3l3-3m0-6a4 4 0 10-8 0 4 4 0 008 0z" />
                </svg>
                <span class="text-sm font-medium">Pilih Bukti Transfer (bisa lebih dari satu)</span>
                <input id="bukti_transfer" type="file" name="bukti_transfer" wire:model="bukti_transfer" multiple
                    class="hidden">
            </label>

            @error('bukti_transfer.*')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror

            <button type="submit"
                class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded shadow-md transition duration-200">
                Kirim Bukti Transfer
            </button>
        </form>
    @endif

    {{-- Preview gambar yang sudah diupload (dari database) --}}
    @if ($pembelian && $pembelian->images)
        <div class="mt-4 text-xs text-gray-500">Gambar yang sudah diupload:</div>
        <div class="flex flex-wrap gap-2 mt-2">
            @foreach ($pembelian->images as $i => $img)
                <div class="relative group">
                    <div class="relative">
                        <img src="{{ asset('storage/' . $img) }}"
                            class="h-32 w-32 object-cover rounded border cursor-pointer hover:opacity-80 transition-all duration-200 hover:scale-105"
                            onclick="Livewire.find('{{ $this->getId() }}').call('showImageModal', '{{ asset('storage/' . $img) }}')" />
                        <div
                            class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200 rounded flex items-center justify-center pointer-events-none">
                            <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                            </svg>
                        </div>
                    </div>
                    <button type="button" wire:click="removeUploaded({{ $i }})"
                        class="absolute top-1 right-1 bg-red-600 text-red-700 rounded-full w-12 h-12 flex items-center justify-center text-xs opacity-80 group-hover:opacity-100 z-10">&times;</button>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Modal untuk preview gambar full --}}
    @if ($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4"
            onclick="Livewire.find('{{ $this->getId() }}').call('hideImageModal')"
            onkeydown="if(event.key === 'Escape') Livewire.find('{{ $this->getId() }}').call('hideImageModal')"
            tabindex="0">
            <div class="relative max-w-5xl max-h-full w-full h-full flex items-center justify-center">
                <img src="{{ $modalImageUrl }}"
                    class="max-w-full w-auto max-h-[80vh] object-contain rounded-lg shadow-2xl"
                    onclick="event.stopPropagation()" />
                <button onclick="Livewire.find('{{ $this->getId() }}').call('hideImageModal')"
                    class="absolute top-4 right-4 bg-black bg-opacity-50 hover:bg-opacity-70 text-white rounded-full w-12 h-12 flex items-center justify-center transition-all duration-200 hover:scale-110">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
                <div
                    class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-50 text-white px-4 py-2 rounded-lg text-sm">
                    Klik di luar gambar atau tekan ESC untuk menutup
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    Livewire.find('{{ $this->getId() }}').call('hideImageModal');
                }
            });
        </script>
    @endif
</div>

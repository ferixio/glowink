<?php

/**
 * Contoh penggunaan Date Helper Functions
 * File ini hanya untuk demonstrasi, tidak perlu di-include
 */

// Contoh 1: Format tanggal sederhana
$tanggal = '2024-01-15';
echo format_tanggal_indonesia($tanggal); // Output: 15 Januari 2024

// Contoh 2: Format tanggal tanpa tahun
echo format_tanggal_indonesia($tanggal, false); // Output: 15 Januari

// Contoh 3: Format tanggal dengan waktu
$tanggalWaktu = '2024-01-15 14:30:00';
echo format_tanggal_waktu_indonesia($tanggalWaktu); // Output: 15 Januari 2024 14:30

// Contoh 4: Format tanggal singkat
echo format_tanggal_indonesia_singkat($tanggal); // Output: 15 Jan 2024

// Contoh 5: Format tanggal dengan hari
echo format_tanggal_indonesia_dengan_hari($tanggal); // Output: Senin, 15 Januari 2024

// Contoh 6: Menggunakan dengan Carbon
use Carbon\Carbon;
$carbonDate = Carbon::now();
echo format_tanggal_indonesia($carbonDate); // Output: [tanggal hari ini] [bulan] [tahun]

// Contoh 7: Menggunakan dengan Eloquent Model
// $user = User::find(1);
// echo format_tanggal_indonesia($user->created_at);

// Contoh 8: Handle null values
$nullDate = null;
echo format_tanggal_indonesia($nullDate); // Output: -

// Contoh 9: Menggunakan di array
$dates = [
    '2024-01-15',
    '2024-02-20',
    '2024-03-10',
];

foreach ($dates as $date) {
    echo format_tanggal_indonesia($date) . "\n";
}

// Contoh 10: Format multiple dates dengan different formats
$eventDates = [
    'start_date' => '2024-01-15',
    'end_date' => '2024-01-20',
    'created_at' => '2024-01-10 09:00:00',
];

echo "Event dimulai: " . format_tanggal_indonesia($eventDates['start_date']) . "\n";
echo "Event berakhir: " . format_tanggal_indonesia($eventDates['end_date']) . "\n";
echo "Dibuat pada: " . format_tanggal_waktu_indonesia($eventDates['created_at']) . "\n";

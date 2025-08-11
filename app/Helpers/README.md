# Date Helper Functions

File ini berisi fungsi-fungsi helper untuk memformat tanggal ke format Indonesia yang dapat digunakan di seluruh aplikasi Laravel.

## Cara Penggunaan

### 1. Menggunakan Global Functions (Direkomendasikan)

```php
// Format tanggal lengkap dengan tahun
echo format_tanggal_indonesia('2024-01-15');
// Output: 15 Januari 2024

// Format tanggal tanpa tahun
echo format_tanggal_indonesia('2024-01-15', false);
// Output: 15 Januari

// Format tanggal dengan waktu
echo format_tanggal_waktu_indonesia('2024-01-15 14:30:00');
// Output: 15 Januari 2024 14:30

// Format tanggal singkat
echo format_tanggal_indonesia_singkat('2024-01-15');
// Output: 15 Jan 2024

// Format tanggal dengan hari
echo format_tanggal_indonesia_dengan_hari('2024-01-15');
// Output: Senin, 15 Januari 2024
```

### 2. Menggunakan Class DateHelper

```php
use App\Helpers\DateHelper;

// Format tanggal lengkap
echo DateHelper::toIndonesianDate('2024-01-15');

// Format tanggal dengan waktu
echo DateHelper::toIndonesianDateTime('2024-01-15 14:30:00');

// Format tanggal singkat
echo DateHelper::toIndonesianDateShort('2024-01-15');

// Format tanggal dengan hari
echo DateHelper::toIndonesianDateWithDay('2024-01-15');
```

### 3. Penggunaan di Blade Templates

```php
{{ format_tanggal_indonesia($user->created_at) }}
{{ format_tanggal_waktu_indonesia($order->created_at) }}
{{ format_tanggal_indonesia_singkat($post->published_at) }}
{{ format_tanggal_indonesia_dengan_hari($event->date) }}
```

### 4. Penggunaan di Filament Resources

```php
Tables\Columns\TextColumn::make('created_at')
    ->label('Tanggal Dibuat')
    ->formatStateUsing(function ($record) {
        return format_tanggal_indonesia($record->created_at);
    }),
```

### 5. Penggunaan di Controllers

```php
public function show($id)
{
    $user = User::find($id);
    $formattedDate = format_tanggal_indonesia($user->created_at);

    return view('users.show', compact('user', 'formattedDate'));
}
```

## Parameter Input

Fungsi-fungsi ini dapat menerima input dalam berbagai format:

-   **String**: `'2024-01-15'`, `'2024-01-15 14:30:00'`
-   **Carbon Object**: `Carbon::now()`
-   **Eloquent Model Date**: `$user->created_at`
-   **Timestamp**: `1642233600`

## Fitur

-   ✅ Otomatis mendeteksi format input
-   ✅ Handle null/empty values dengan return '-'
-   ✅ Support untuk Carbon objects
-   ✅ Multiple format output (lengkap, singkat, dengan hari, dengan waktu)
-   ✅ Reusable di seluruh aplikasi
-   ✅ Tidak memerlukan import di setiap file

## Catatan

Setelah menambahkan helper functions, pastikan untuk menjalankan:

```bash
composer dump-autoload
```

untuk memuat fungsi-fungsi helper yang baru.

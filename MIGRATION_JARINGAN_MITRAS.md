# Migrasi Data Jaringan Mitra

Dokumentasi untuk migrasi data dari tabel `up` (database lama) ke tabel `jaringan_mitras` (database baru).

## Struktur Tabel

### Tabel Lama (`up`)

-   `id` - Primary key
-   `id_mem` - Username member (string)
-   `id_sponsor` - Username sponsor (string)
-   `id_up` - ID up
-   `status` - Status (1 = aktif)

### Tabel Baru (`jaringan_mitras`)

-   `id` - Primary key
-   `user_id` - ID user (integer, foreign key ke users.id)
-   `sponsor_id` - ID sponsor (integer, foreign key ke users.id)
-   `level` - Level dalam jaringan (1 = direct sponsor, 2 = sponsor dari sponsor, dst)

## Command yang Tersedia

### 1. MigrateUpToJaringanMitras

**Command:** `php artisan migrate:up-to-jaringan`

**Fungsi:** Memigrasikan data dari tabel `up` ke `jaringan_mitras`

**Fitur:**

-   Mapping username ke user_id otomatis
-   Perhitungan level berdasarkan jalur sponsor
-   Validasi data untuk mencegah duplikasi
-   Batch insert untuk performa optimal
-   Transaction untuk keamanan data
-   Pencegahan loop dan circular reference

**Output:**

-   Total data yang dimigrasi
-   Data yang dilewati (dengan alasan)
-   Progress real-time

## Cara Penggunaan

### Langkah 1: Persiapan

Pastikan:

1. Database lama (`mysql_old`) sudah dikonfigurasi
2. Tabel `users` sudah terisi data
3. Tabel `jaringan_mitras` sudah dibuat

### Langkah 2: Jalankan Migrasi

```bash
php artisan migrate:up-to-jaringan
```

### Langkah 3: Validasi Data

```bash
php artisan migrate:validate-jaringan
```

### Langkah 4: Jika Ada Masalah (Opsional)

```bash
# Jika ada masalah, hapus data secara manual di database
# TRUNCATE TABLE jaringan_mitras;

# Jalankan ulang migrasi
php artisan migrate:up-to-jaringan
```

## Algoritma Perhitungan Level

Level dihitung berdasarkan jalur sponsor:

-   **Level 1:** Direct sponsor (sponsor langsung)
-   **Level 2:** Sponsor dari sponsor
-   **Level 3:** Sponsor dari sponsor dari sponsor
-   Dan seterusnya...

**Contoh:**

```
A → B → C → D
```

-   B adalah level 1 dari A
-   C adalah level 2 dari A
-   D adalah level 3 dari A

## Fitur Keamanan

1. **Transaction:** Semua operasi dalam transaction untuk rollback otomatis jika ada error
2. **Loop Prevention:** Mencegah infinite loop dalam perhitungan level
3. **Duplicate Prevention:** Mencegah data duplikat
4. **Validation:** Validasi data sebelum insert
5. **Batch Processing:** Insert data dalam batch untuk performa optimal

## Troubleshooting

### Error: "Data dilewati karena user tidak ditemukan"

-   Pastikan semua username di tabel `up` sudah ada di tabel `users` dengan field `id_mitra`
-   Jalankan migrasi user terlebih dahulu jika belum

### Error: "Loop terdeteksi"

-   Data di tabel `up` memiliki circular reference
-   Periksa data di database lama

### Error: "Level maksimal tercapai"

-   Ada jalur sponsor yang sangat panjang (>20 level)
-   Periksa data di database lama

## Monitoring

Gunakan command validasi untuk memantau kualitas data:

```bash
php artisan migrate:validate-jaringan
```

Command ini akan memberikan laporan lengkap tentang:

-   Total data
-   Data yang tidak valid
-   Duplikasi
-   Distribusi level
-   Circular reference

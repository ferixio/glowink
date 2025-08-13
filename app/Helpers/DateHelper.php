<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Format tanggal ke format Indonesia (contoh: 15 Januari 2024)
     *
     * @param string|Carbon $date
     * @param bool $includeYear
     * @return string
     */
    public static function toIndonesianDate($date, $includeYear = true)
    {
        if (!$date) {
            return '-';
        }

        // Convert to Carbon if it's a string
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        // Array nama bulan dalam bahasa Indonesia
        $bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $tanggal = $date->day;
        $bulanIndex = $date->month;
        $tahun = $date->year;

        if ($includeYear) {
            return $tanggal . ' ' . $bulan[$bulanIndex] . ' ' . $tahun;
        }

        return $tanggal . ' ' . $bulan[$bulanIndex];
    }

    /**
     * Format tanggal ke format Indonesia dengan waktu (contoh: 15 Januari 2024 14:30)
     *
     * @param string|Carbon $date
     * @return string
     */
    public static function toIndonesianDateTime($date)
    {
        if (!$date) {
            return '-';
        }

        $dateFormatted = self::toIndonesianDate($date);
        $time = Carbon::parse($date)->format('H:i');

        return $dateFormatted . ' ' . $time;
    }

    /**
     * Format tanggal ke format Indonesia singkat (contoh: 15 Jan 2024)
     *
     * @param string|Carbon $date
     * @return string
     */
    public static function toIndonesianDateShort($date)
    {
        if (!$date) {
            return '-';
        }

        // Convert to Carbon if it's a string
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        // Array nama bulan singkat dalam bahasa Indonesia
        $bulanSingkat = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Ags',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
        ];

        $tanggal = $date->day;
        $bulanIndex = $date->month;
        $tahun = $date->year;

        return $tanggal . ' ' . $bulanSingkat[$bulanIndex] . ' ' . $tahun;
    }

    /**
     * Format tanggal ke format Indonesia dengan hari (contoh: Senin, 15 Januari 2024)
     *
     * @param string|Carbon $date
     * @return string
     */
    public static function toIndonesianDateWithDay($date)
    {
        if (!$date) {
            return '-';
        }

        // Convert to Carbon if it's a string
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        // Array nama hari dalam bahasa Indonesia
        $hari = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        $hariIndex = $date->dayOfWeek;
        $tanggal = $date->day;
        $bulanIndex = $date->month;
        $tahun = $date->year;

        // Array nama bulan dalam bahasa Indonesia
        $bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $hari[$hariIndex] . ', ' . $tanggal . ' ' . $bulan[$bulanIndex] . ' ' . $tahun;
    }
}

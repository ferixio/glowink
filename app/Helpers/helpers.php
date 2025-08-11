<?php

use App\Helpers\DateHelper;

if (!function_exists('format_tanggal_indonesia')) {
    /**
     * Format tanggal ke format Indonesia (contoh: 15 Januari 2024)
     *
     * @param string|Carbon $date
     * @param bool $includeYear
     * @return string
     */
    function format_tanggal_indonesia($date, $includeYear = true)
    {
        return DateHelper::toIndonesianDate($date, $includeYear);
    }
}

if (!function_exists('format_tanggal_waktu_indonesia')) {
    /**
     * Format tanggal ke format Indonesia dengan waktu (contoh: 15 Januari 2024 14:30)
     *
     * @param string|Carbon $date
     * @return string
     */
    function format_tanggal_waktu_indonesia($date)
    {
        return DateHelper::toIndonesianDateTime($date);
    }
}

if (!function_exists('format_tanggal_indonesia_singkat')) {
    /**
     * Format tanggal ke format Indonesia singkat (contoh: 15 Jan 2024)
     *
     * @param string|Carbon $date
     * @return string
     */
    function format_tanggal_indonesia_singkat($date)
    {
        return DateHelper::toIndonesianDateShort($date);
    }
}

if (!function_exists('format_tanggal_indonesia_dengan_hari')) {
    /**
     * Format tanggal ke format Indonesia dengan hari (contoh: Senin, 15 Januari 2024)
     *
     * @param string|Carbon $date
     * @return string
     */
    function format_tanggal_indonesia_dengan_hari($date)
    {
        return DateHelper::toIndonesianDateWithDay($date);
    }
}

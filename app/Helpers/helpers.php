<?php

use App\Helpers\DateHelper;
use App\Services\JaringanMitraService;

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

if (!function_exists('create_jaringan_mitra')) {
    /**
     * Helper function untuk membuat jaringan mitra
     *
     * @param \App\Models\User $user User yang baru dibuat
     * @param int|null $sponsorId ID sponsor (bisa null jika tidak ada sponsor)
     * @return void
     */
    function create_jaringan_mitra($user, $sponsorId = null)
    {
        JaringanMitraService::createJaringanMitra($user, $sponsorId);
    }
}

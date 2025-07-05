<?php

namespace App\Services;

class LocationService
{
    public static function getProvinces(): array
    {
        $provinces = [];
        $csvPath = public_path('data/provinces.csv');

        if (file_exists($csvPath)) {
            $handle = fopen($csvPath, 'r');
            while (($data = fgetcsv($handle)) !== false) {
                $provinces[$data[0]] = $data[1];
            }
            fclose($handle);
        }

        return $provinces;
    }

    public static function getRegencies(): array
    {
        $regencies = [];
        $csvPath = public_path('data/regencies.csv');

        if (file_exists($csvPath)) {
            $handle = fopen($csvPath, 'r');
            while (($data = fgetcsv($handle)) !== false) {
                $regencies[$data[1]][] = [
                    'id' => $data[0],
                    'name' => $data[2],
                ];
            }
            fclose($handle);
        }

        return $regencies;
    }

    public static function getRegenciesByProvince(string $provinceId): array
    {
        $regencies = self::getRegencies();
        return $regencies[$provinceId] ?? [];
    }
}

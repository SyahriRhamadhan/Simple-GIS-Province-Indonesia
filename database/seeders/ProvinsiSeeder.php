<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProvinsiSeeder extends Seeder
{
    public function run()
    {
        // Cek apakah file GeoJSON tersedia
        $filePath = database_path('geojson/indonesia-province.json');
        if (!File::exists($filePath)) {
            throw new \Exception("File GeoJSON tidak ditemukan: $filePath");
        }

        // Baca file GeoJSON
        $file = File::get($filePath);
        $geojson = json_decode($file, true);

        if (!$geojson || !isset($geojson['features'])) {
            throw new \Exception("Format GeoJSON tidak valid");
        }

        foreach ($geojson['features'] as $feature) {
            DB::table('provinsis')->insert([
                'nama'    => $feature['properties']['NAME_1'] ?? 'Unknown',
                // Simpan seluruh feature (termasuk properties dan geometry)
                'geojson' => json_encode($feature)
            ]);
        }        
    }
}

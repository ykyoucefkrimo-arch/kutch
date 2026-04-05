<?php
namespace Database\Seeders;

use App\Models\Commune;
use App\Models\Wilaya;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class WilayaCommuneSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/cities.json');

        if (!File::exists($path)) {
            $this->command->error("cities.json not found at: {$path}");
            return;
        }

        $data = json_decode(File::get($path), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('Invalid JSON in cities.json: ' . json_last_error_msg());
            return;
        }

        // Build a map: wilaya_id (JSON) => Wilaya (DB)
        $wilayaMap = [];

        foreach ($data['wilayas'] as $w) {
            $wilaya = Wilaya::firstOrCreate(
                ['code' => $w['wilaya_id']],
                [
                    'name'    => $w['wilaya_name_latin'],
                    'name_ar' => $w['wilaya_name_arabic'],
                ]
            );
            $wilayaMap[$w['wilaya_id']] = $wilaya->id;
        }

        foreach ($data['communes'] as $c) {
            if (!isset($wilayaMap[$c['wilaya_id']])) continue;

            Commune::firstOrCreate(
                [
                    'wilaya_id' => $wilayaMap[$c['wilaya_id']],
                    'name'      => $c['commune_name_latin'],
                ],
                [
                    'name_ar' => $c['commune_name_arabic'],
                ]
            );
        }

        $this->command->info('Wilayas and communes seeded successfully.');
    }
}

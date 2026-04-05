<?php
namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Chambres à coucher',
                'slug'        => 'chambres-coucher',
                'description' => 'Mobilier de chambre à coucher fabriqué sur mesure en bois massif.',
                'is_active'   => true,
                'sort_order'  => 1,
            ],
            [
                'name'        => 'Salons & Séjours',
                'slug'        => 'salons-sejours',
                'description' => 'Meubles de salon et de séjour élégants et robustes.',
                'is_active'   => true,
                'sort_order'  => 2,
            ],
            [
                'name'        => 'Cuisines équipées',
                'slug'        => 'cuisines-equipees',
                'description' => 'Cuisines modernes et classiques entièrement équipées.',
                'is_active'   => true,
                'sort_order'  => 3,
            ],
            [
                'name'        => 'Bibliothèques & Bureaux',
                'slug'        => 'bibliotheques-bureaux',
                'description' => 'Bibliothèques, bureaux et espaces de travail sur mesure.',
                'is_active'   => true,
                'sort_order'  => 4,
            ],
            [
                'name'        => 'Portes & Fenêtres',
                'slug'        => 'portes-fenetres',
                'description' => 'Portes intérieures et extérieures, fenêtres en bois travaillé.',
                'is_active'   => true,
                'sort_order'  => 5,
            ],
            [
                'name'        => 'Mobilier sur mesure',
                'slug'        => 'mobilier-sur-mesure',
                'description' => 'Toute création de mobilier sur mesure selon vos besoins.',
                'is_active'   => true,
                'sort_order'  => 6,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        $this->command->info('Categories seeded successfully.');
    }
}

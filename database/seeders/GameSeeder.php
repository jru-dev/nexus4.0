<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\GameCategory;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener categorías
        $accion = GameCategory::where('slug', 'accion')->first();
        $aventura = GameCategory::where('slug', 'aventura')->first();
        $supervivencia = GameCategory::where('slug', 'supervivencia')->first();
        $terror = GameCategory::where('slug', 'terror')->first();
        $estrategia = GameCategory::where('slug', 'estrategia')->first();

$games = [
        // Juegos de Acción
        [
            'title' => 'Call of Duty: Modern Warfare III',
            'slug' => 'call-of-duty-modern-warfare-iii',
            'description' => 'La última entrega de la famosa saga de disparos en primera persona. Combate intenso y gráficos de última generación.',
            'price' => 249.99,
            'developer' => 'Infinity Ward',
            'publisher' => 'Activision',
            'release_date' => '2023-11-10',
            'image_url' => 'images/games/covers/call-of-duty-mw3.jpg',
            'screenshots' => [
                'images/games/screenshots/call-of-duty-mw3/imagen-1.jpg', 
                'images/games/screenshots/call-of-duty-mw3/imagen-2.jpg', 
                'images/games/screenshots/call-of-duty-mw3/imagen-3.jpg',
                'images/games/screenshots/call-of-duty-mw3/imagen-4.jpg',
                'images/games/screenshots/call-of-duty-mw3/imagen-5.jpg'
            ],
            'system_requirements' => [
                'minimum' => [
                    'os' => 'Windows 10 64-bit',
                    'processor' => 'Intel Core i5-6600K / AMD Ryzen 5 1400',
                    'memory' => '8 GB RAM',
                    'graphics' => 'NVIDIA GeForce GTX 960 / AMD Radeon RX 470',
                    'storage' => '149 GB'
                ],
                'recommended' => [
                    'os' => 'Windows 11 64-bit',
                    'processor' => 'Intel Core i7-8700K / AMD Ryzen 7 2700X',
                    'memory' => '16 GB RAM',
                    'graphics' => 'NVIDIA GeForce RTX 3060 / AMD Radeon RX 6600',
                    'storage' => '149 GB SSD'
                ]
            ],
            'age_rating' => 'M',
            'is_active' => true,
            'category_id' => $accion->id,
        ],
        [
            'title' => 'Cyberpunk 2077',
            'slug' => 'cyberpunk-2077',
            'description' => 'Un RPG de acción en un mundo abierto futurista donde juegas como un mercenario en Night City.',
            'price' => 199.99,
            'developer' => 'CD Projekt Red',
            'publisher' => 'CD Projekt',
            'release_date' => '2020-12-10',
            'image_url' => 'images/games/covers/cyberpunk-2077.jpg',
            'screenshots' => [
                'images/games/screenshots/cyberpunk-2077/imagen-1.jpg', 
                'images/games/screenshots/cyberpunk-2077/imagen-2.jpg',
                'images/games/screenshots/cyberpunk-2077/imagen-3.jpg',
                'images/games/screenshots/cyberpunk-2077/imagen-4.jpg',
                'images/games/screenshots/cyberpunk-2077/imagen-5.jpg'
            ],
            'system_requirements' => [
                'minimum' => [
                    'os' => 'Windows 10 64-bit',
                    'processor' => 'Intel Core i5-3570K / AMD FX-8310',
                    'memory' => '8 GB RAM',
                    'graphics' => 'NVIDIA GeForce GTX 970 / AMD Radeon RX 470',
                    'storage' => '70 GB'
                ]
            ],
            'age_rating' => 'M',
            'is_active' => true,
            'category_id' => $accion->id,
        ],

        // Juegos de Aventura
        [
            'title' => 'The Legend of Zelda: Tears of the Kingdom',
            'slug' => 'the-legend-of-zelda-tears-of-the-kingdom',
            'description' => 'La secuela de Breath of the Wild. Explora los cielos de Hyrule en una aventura épica.',
            'price' => 279.99,
            'developer' => 'Nintendo EPD',
            'publisher' => 'Nintendo',
            'release_date' => '2023-05-12',
            'image_url' => 'images/games/covers/Zelda-tears-of-kingdom.jpg.webp',
            'screenshots' => [
                'images/games/screenshots/zelda-tears-of-kingdom/imagen-1.jpg', 
                'images/games/screenshots/zelda-tears-of-kingdom/imagen-2.jpg',
                'images/games/screenshots/zelda-tears-of-kingdom/imagen-3.jpg',
                'images/games/screenshots/zelda-tears-of-kingdom/imagen-4.jpg',
                'images/games/screenshots/zelda-tears-of-kingdom/imagen-5.jpg'
            ],
            'system_requirements' => [
                'minimum' => [
                    'console' => 'Nintendo Switch',
                    'storage' => '18.2 GB'
                ]
            ],
            'age_rating' => 'E10+',
            'is_active' => true,
            'category_id' => $aventura->id,
        ],
        [
            'title' => 'Horizon Forbidden West',
            'slug' => 'horizon-forbidden-west',
            'description' => 'Únete a Aloy mientras explora las Tierras Prohibidas, un mundo post-apocalíptico lleno de máquinas peligrosas.',
            'price' => 229.99,
            'developer' => 'Guerrilla Games',
            'publisher' => 'Sony Interactive Entertainment',
            'release_date' => '2022-02-18',
            'image_url' => 'images/games/covers/Horizon-FW.jpg',
            'screenshots' => [
                'images/games/screenshots/horizon-forbidden-west/imagen-1.jpg', 
                'images/games/screenshots/horizon-forbidden-west/imagen-2.jpg',
                'images/games/screenshots/horizon-forbidden-west/imagen-3.jpg',
                'images/games/screenshots/horizon-forbidden-west/imagen-4.jpg',
                'images/games/screenshots/horizon-forbidden-west/imagen-5.jpg'
            ],
            'system_requirements' => [
                'minimum' => [
                    'os' => 'Windows 10 64-bit',
                    'processor' => 'Intel Core i5-8400 / AMD Ryzen 5 2600',
                    'memory' => '16 GB RAM',
                    'graphics' => 'NVIDIA GeForce GTX 1060 / AMD Radeon RX 580',
                    'storage' => '150 GB'
                ]
            ],
            'age_rating' => 'T',
            'is_active' => true,
            'category_id' => $aventura->id,
        ],

        // Juegos de Supervivencia
        [
            'title' => 'Subnautica',
            'slug' => 'subnautica',
            'description' => 'Sumérgete en un océano alienígena en este juego de supervivencia y exploración submarina.',
            'price' => 89.99,
            'developer' => 'Unknown Worlds Entertainment',
            'publisher' => 'Unknown Worlds Entertainment',
            'release_date' => '2018-01-23',
            'image_url' => 'images/games/covers/Subnautica.jpg',
            'screenshots' => [
                'images/games/screenshots/subnautica/imagen-1.jpg', 
                'images/games/screenshots/subnautica/imagen-2.jpg',
                'images/games/screenshots/subnautica/imagen-3.jpg',
                'images/games/screenshots/subnautica/imagen-4.jpg',
                'images/games/screenshots/subnautica/imagen-5.jpg'
            ],
            'system_requirements' => [
                'minimum' => [
                    'os' => 'Windows Vista SP2 / 7 / 8 / 10',
                    'processor' => 'Intel Haswell 2 cores / 4 threads @ 2.5Ghz',
                    'memory' => '4 GB RAM',
                    'graphics' => 'Intel HD 4600 / AMD R5 M230',
                    'storage' => '20 GB'
                ]
            ],
            'age_rating' => 'E10+',
            'is_active' => false, // Este aparece como oculto en tu admin panel
            'category_id' => $supervivencia->id,
        ],
        [
            'title' => 'The Forest',
            'slug' => 'the-forest',
            'description' => 'Sobrevive en un bosque misterioso lleno de caníbales mutantes después de un accidente aéreo.',
            'price' => 69.99,
            'developer' => 'Endnight Games Ltd',
            'publisher' => 'Endnight Games Ltd',
            'release_date' => '2018-04-30',
            'image_url' => 'images/games/covers/The-forest.jpg',
            'screenshots' => [
                'images/games/screenshots/the-forest/imagen-1.jpg', 
                'images/games/screenshots/the-forest/imagen-2.jpg',
                'images/games/screenshots/the-forest/imagen-3.jpg',
                'images/games/screenshots/the-forest/imagen-4.jpg',
                'images/games/screenshots/the-forest/imagen-5.jpg'
            ],
            'system_requirements' => [
                'minimum' => [
                    'os' => 'Windows 7',
                    'processor' => 'Intel Dual-Core 2.4 GHz / AMD Dual-Core 2.8 GHz',
                    'memory' => '4 GB RAM',
                    'graphics' => 'NVIDIA GeForce 8800GT / AMD Radeon HD 2600 Pro',
                    'storage' => '5 GB'
                ]
            ],
            'age_rating' => 'M',
            'is_active' => true,
            'category_id' => $supervivencia->id,
        ],

        // Juegos de Terror
        [
            'title' => 'Phasmophobia',
            'slug' => 'phasmophobia',
            'description' => 'Un juego de terror cooperativo donde investigas actividad paranormal como cazafantasmas.',
            'price' => 45.99,
            'developer' => 'Kinetic Games',
            'publisher' => 'Kinetic Games',
            'release_date' => '2020-09-18',
            'image_url' => 'images/games/covers/Phasmophobia.jpg',
            'screenshots' => [
                'images/games/screenshots/phasmophobia/imagen-1.jpg', 
                'images/games/screenshots/phasmophobia/imagen-2.jpg',
                'images/games/screenshots/phasmophobia/imagen-3.jpg',
                'images/games/screenshots/phasmophobia/imagen-4.jpg',
                'images/games/screenshots/phasmophobia/imagen-5.jpg'
            ],
            'system_requirements' => [
                'minimum' => [
                    'os' => 'Windows 10 64Bit',
                    'processor' => 'Intel Core i5-4590 / AMD FX 8350',
                    'memory' => '8 GB RAM',
                    'graphics' => 'NVIDIA GTX 970 / AMD Radeon R9 290',
                    'storage' => '16 GB'
                ]
            ],
            'age_rating' => 'T',
            'is_active' => true,
            'category_id' => $terror->id,
        ],
        [
            'title' => 'Resident Evil 4 Remake',
            'slug' => 'resident-evil-4-remake',
            'description' => 'La reimaginación completa del clásico juego de terror y supervivencia con gráficos modernos.',
            'price' => 259.99,
            'developer' => 'Capcom',
            'publisher' => 'Capcom',
            'release_date' => '2023-03-24',
            'image_url' => 'images/games/covers/Resident_evil.jpg',
            'screenshots' => [
                'images/games/screenshots/resident-evil-4-remake/imagen-1.jpg', 
                'images/games/screenshots/resident-evil-4-remake/imagen-2.jpg',
                'images/games/screenshots/resident-evil-4-remake/imagen-3.jpg',
                'images/games/screenshots/resident-evil-4-remake/imagen-4.jpg',
                'images/games/screenshots/resident-evil-4-remake/imagen-5.jpg'
            ],
            'system_requirements' => [
                'minimum' => [
                    'os' => 'Windows 10 64-bit',
                    'processor' => 'AMD Ryzen 5 3600 / Intel Core i5 10400',
                    'memory' => '16 GB RAM',
                    'graphics' => 'AMD Radeon RX 5700 / NVIDIA GeForce GTX 1070',
                    'storage' => '67 GB'
                ]
            ],
            'age_rating' => 'M',
            'is_active' => true,
            'category_id' => $terror->id,
        ],

        // Juegos de Estrategia
        [
            'title' => 'Age of Empires IV',
            'slug' => 'age-of-empires-iv',
            'description' => 'El regreso de la legendaria serie de estrategia en tiempo real con civilizaciones históricas.',
            'price' => 189.99,
            'developer' => 'Relic Entertainment',
            'publisher' => 'Xbox Game Studios',
            'release_date' => '2021-10-28',
            'image_url' => 'images/games/covers/AOE-IV.jpg',
            'screenshots' => [
                'images/games/screenshots/age-of-empires-iv/imagen-1.jpg', 
                'images/games/screenshots/age-of-empires-iv/imagen-2.jpg',
                'images/games/screenshots/age-of-empires-iv/imagen-3.jpg',
                'images/games/screenshots/age-of-empires-iv/imagen-4.jpg',
                'images/games/screenshots/age-of-empires-iv/imagen-5.jpg'
            ],
            'system_requirements' => [
                'minimum' => [
                    'os' => 'Windows 10 64-bit',
                    'processor' => 'Intel Core i5-6300U / AMD Ryzen 5 2400G',
                    'memory' => '8 GB RAM',
                    'graphics' => 'Intel HD 520 / AMD Radeon RX Vega 11',
                    'storage' => '50 GB'
                ]
            ],
            'age_rating' => 'T',
            'is_active' => true,
            'category_id' => $estrategia->id,
        ],
        [
            'title' => 'Civilization VI',
            'slug' => 'civilization-vi',
            'description' => 'Construye un imperio que resistirá la prueba del tiempo en este juego de estrategia por turnos.',
            'price' => 159.99,
            'developer' => 'Firaxis Games',
            'publisher' => '2K Games',
            'release_date' => '2016-10-21',
            'image_url' => 'images/games/covers/Civilization-VI.jpg',
            'screenshots' => [
                'images/games/screenshots/civilization-vi/imagen-1.jpg', 
                'images/games/screenshots/civilization-vi/imagen-2.jpg',
                'images/games/screenshots/civilization-vi/imagen-3.jpg',
                'images/games/screenshots/civilization-vi/imagen-4.jpg',
                'images/games/screenshots/civilization-vi/imagen-5.jpg'
            ],
            'system_requirements' => [
                'minimum' => [
                    'os' => 'Windows 7x64 / Windows 8.1x64 / Windows 10x64',
                    'processor' => 'Intel Core i3 2.5 Ghz / AMD Phenom II 2.6 Ghz',
                    'memory' => '4 GB RAM',
                    'graphics' => '1 GB DirectX 11 Video Card',
                    'storage' => '12 GB'
                ]
            ],
            'age_rating' => 'E10+',
            'is_active' => true,
            'category_id' => $estrategia->id,
        ],
    ];

    foreach ($games as $game) {
        Game::create($game);
    }
}
}
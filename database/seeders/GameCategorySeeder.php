<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GameCategory;

class GameCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Acción',
                'slug' => 'accion',
                'description' => 'Juegos llenos de adrenalina con combates intensos y secuencias de acción rápida.'
            ],
            [
                'name' => 'Aventura',
                'slug' => 'aventura',
                'description' => 'Explora mundos fascinantes y vive historias épicas llenas de misterio y descubrimiento.'
            ],
            [
                'name' => 'Supervivencia',
                'slug' => 'supervivencia',
                'description' => 'Pon a prueba tus habilidades para sobrevivir en entornos hostiles y desafiantes.'
            ],
            [
                'name' => 'Terror',
                'slug' => 'terror',
                'description' => 'Experimenta el miedo y la tensión en atmosferas escalofriantes y terroríficas.'
            ],
            [
                'name' => 'Estrategia',
                'slug' => 'estrategia',
                'description' => 'Planifica, conquista y domina usando tu inteligencia y habilidades tácticas.'
            ]
        ];

        foreach ($categories as $category) {
            GameCategory::create($category);
        }
    }
}
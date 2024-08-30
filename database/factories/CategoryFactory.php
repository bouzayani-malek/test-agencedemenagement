<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word, // Génère un nom aléatoire pour la catégorie
            'is_deleted' => $this->faker->boolean(10), // Valeur aléatoire pour is_deleted, 10% de chances d'être true
        ];
    }
}

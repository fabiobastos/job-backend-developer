<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => 1,
            'name' => $this->faker->sentence(),
            'price' => 400.05,
            'description' => $this->faker->paragraph(),
            'category' => "Malas",
            'image_url' => "http://teste.jpg",
        ];
    }
}

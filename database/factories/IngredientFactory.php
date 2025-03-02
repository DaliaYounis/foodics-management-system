<?php

namespace Database\Factories;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Factories\Factory;

class IngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Ingredient::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word,
            'merchant_id' => 1,
            'alert_threshold_percentage' => 50
        ];
    }}

<?php

namespace Database\Factories;

use App\Enums\RareValueTypes;
use App\Models\WebsiteRareValue;
use App\Models\WebsiteRareValueCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class RareValuesFactory extends Factory
{
    protected $model = WebsiteRareValue::class;

    public function definition(): array
    {
        $randNumber = rand(0, 10000);

        return [
            'category_id' => WebsiteRareValueCategory::inRandomOrder()->first()->id,
            'name' => $this->faker->name,
            'credit_value' => $randNumber > 0 ? $randNumber : null,
            'currency_value' => $randNumber > 0 ? $randNumber : null,
            'currency_type' => $this->faker->randomElement(['duckets', 'diamonds', 'points']),
            'furniture_icon' => 'throne_icon.png',
        ];
    }
}

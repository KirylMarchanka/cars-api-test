<?php

namespace Database\Factories;

use App\Models\CarBrand;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarBrandFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CarBrand::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $userCount = User::query()->count();

        if ($userCount == 0) {
            User::factory()->create();
        }

        return [
            'name' => $this->faker->name(),
            'created_by' => $this->faker->numberBetween(1, $userCount != 0 ? $userCount : 1),
        ];
    }
}

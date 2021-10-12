<?php

namespace Database\Factories;

use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarModelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CarModel::class;

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

        $brandCount = CarBrand::query()->count();

        if ($brandCount == 0) {
            CarBrand::factory()->create();
        }

        return [
            'name' => $this->faker->name(),
            'brand_id' => $this->faker->numberBetween(1, $brandCount != 0 ? $brandCount : 1),
            'created_by' => $this->faker->numberBetween(1, $userCount != 0 ? $userCount : 1),
        ];
    }
}

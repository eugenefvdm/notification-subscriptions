<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Eugenefvdm\NotificationSubscriptions\Tests\TestModels\Product;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'price' => $this->faker->randomFloat(2, 10, 100),
        ];
    }
}

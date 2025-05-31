<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Eugenefvdm\NotificationSubscriptions\Tests\TestModels\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'remember_token' => \Illuminate\Support\Str::random(10),
        ];
    }
} 
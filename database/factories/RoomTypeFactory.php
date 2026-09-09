<?php

namespace Database\Factories;

use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomTypeFactory extends Factory
{
    protected $model = RoomType::class;
    public function definition(): array { return ['name' => $this->faker->unique()->words(2, true), 'is_featured' => false]; }
}

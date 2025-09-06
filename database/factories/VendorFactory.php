<?php

namespace Database\Factories;

use App\Enums\VendorStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vendor>
 */
class VendorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         return [
            'user_id' => User::factory(), // automatically creates a linked user
            'status' => VendorStatusEnum::Active, // use enum instead of string
            'store_name' => $this->faker->company() . ' Store',
            'store_address' => $this->faker->address(),
        ];
    }
}

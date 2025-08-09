<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\RolesEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            "name" => "user",
            "email" => "user@example.com",

        ])->assignRole(RolesEnum::User);
        User::factory()->create([
            "name" => "vendor",
            "email" => "vendor@example.com",

        ])->assignRole(RolesEnum::Vendor);
        User::factory()->create([
            "name" => "admin",
            "email" => "admin@example.com",

        ])->assignRole(RolesEnum::Admin);
    }
}

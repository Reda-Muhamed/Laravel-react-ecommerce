<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\RolesEnum;
use App\Enums\VendorStatusEnum;
use App\Models\Vendor;
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
        $user = User::factory()->create([
            "name" => "vendor",
            "email" => "vendor@example.com",

        ]);
        $user->assignRole(RolesEnum::Vendor);
        Vendor::factory()->create([
            'user_id'=>$user->id,
            'name'=> 'vendor',
            'status'=>VendorStatusEnum::Active,
            'store_name'=>'Vendor Store',
            'store_address'=> fake()->address(),
        ]);
        User::factory()->create([
            "name" => "admin",
            "email" => "admin@example.com",

        ])->assignRole(RolesEnum::Admin);
    }
}

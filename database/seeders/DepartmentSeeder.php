<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                "name" => "Electronics",
                "slug" => Str::slug("Electronics"),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Fashion",
                "slug" => Str::slug("Fashion"),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Home & Kitchen",
                "slug" => Str::slug("Home & Kitchen"),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Books",
                "slug" => Str::slug("Books"),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Sports & Outdoors",
                "slug" => Str::slug("Sports & Outdoors"),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Beauty & Personal Care",
                "slug" => Str::slug("Beauty & Personal Care"),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Toys & Games",
                "slug" => Str::slug("Toys & Games"),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Grocery & Food",
                "slug" => Str::slug("Grocery & Food"),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Health & Wellness",
                "slug" => Str::slug("Health & Wellness"),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Automotive",
                "slug" => Str::slug("Automotive"),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Pet Supplies",
                "slug" => Str::slug("Pet Supplies"),
                "active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "Music & Instruments", "slug" => Str::slug("Music & Instruments"), "active" => true, "created_at" => now(), "updated_at" => now()],
            [
                "name" => "Office & Stationery", "slug" => Str::slug("Office & Stationery"), "active" => true, "created_at" => now(), "updated_at" => now()],
            [
                "name" => "Jewelry & Watches", "slug" => Str::slug("Jewelry & Watches"), "active" => true, "created_at" => now(), "updated_at" => now()],
            [
                "name" => "Baby Products", "slug" => Str::slug("Baby Products"), "active" => true, "created_at" => now(), "updated_at" => now()],
        ];

        DB::table('departments')->insert($departments);
    }
}

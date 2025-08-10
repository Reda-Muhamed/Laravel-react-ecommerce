<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $categories = [
            // Electronics
            [
                "name" => "Computers",
                "department_id" => 1,
                "active" => true,
                "parent_id" => null,
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Laptops",
                "department_id" => 1,
                "active" => true,
                "parent_id" => 1, // Assuming "Computers" gets ID=1 in categories table
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Desktops",
                "department_id" => 1,
                "active" => true,
                "parent_id" => 1,
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Smartphones",
                "department_id" => 1,
                "active" => true,
                "parent_id" => null,
                "created_at" => now(),
                "updated_at" => now(),
            ],

            // Fashion
            [
                "name" => "Men's Clothing",
                "department_id" => 2,
                "active" => true,
                "parent_id" => null,
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Women's Clothing",
                "department_id" => 2,
                "active" => true,
                "parent_id" => null,
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Shoes",
                "department_id" => 2,
                "active" => true,
                "parent_id" => null,
                "created_at" => now(),
                "updated_at" => now(),
            ],

            // Home & Kitchen
            [
                "name" => "Furniture",
                "department_id" => 3,
                "active" => true,
                "parent_id" => null,
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Kitchen Appliances",
                "department_id" => 3,
                "active" => true,
                "parent_id" => null,
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => "Cookware",
                "department_id" => 3,
                "active" => true,
                "parent_id" => 6, // Assuming Kitchen Appliances gets ID=9 in categories
                "created_at" => now(),
                "updated_at" => now(),
            ],
        ];
        DB::table("categories")->insert($categories);
    }
}

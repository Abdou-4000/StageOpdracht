<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\Category;
use App\Models\City;
use Faker\Factory as Faker;

class TeacherSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('nl_BE');
        
        // Create some cities if needed
        $cities = City::all();
        // Update the cities creation part
        if ($cities->count() < 5) {
            $belgianCities = [
                ['name' => 'Brussels', 'zipcode' => 1000],
                ['name' => 'Antwerp', 'zipcode' => 2000],
                ['name' => 'Ghent', 'zipcode' => 9000],
                ['name' => 'Bruges', 'zipcode' => 8000],
                ['name' => 'Leuven', 'zipcode' => 3000]
            ];
            
            foreach ($belgianCities as $cityData) {
                City::create($cityData);
            }
            
            $cities = City::all();
        }
        
        // Create some categories if needed
        $categories = Category::all();
        $categories = Category::all();
        if ($categories->count() < 5) {
            $teachingCategories = [
                ['name' => 'Mathematics'],
                ['name' => 'Languages'],
                ['name' => 'Science'],
                ['name' => 'Arts'],
                ['name' => 'Music'],
                ['name' => 'Physical Education']
            ];
            
            foreach ($teachingCategories as $categoryData) {
                Category::create($categoryData);
            }
            
            $categories = Category::all();
        }
        
        // Create 50 test teachers
        $this->command->info('Creating 50 test teachers...');
        
        for ($i = 0; $i < 50; $i++) {
            // Select a random city
            $city = $cities->random();
            
            // Create teacher with essential fields
            $teacher = Teacher::create([
                'firstname' => $faker->firstName(),
                'lastname' => $faker->lastName(),
                'email' => $faker->unique()->safeEmail(),
                'phone' => substr(str_replace(' ', '', $faker->phoneNumber()), 0, 15),
                'companynumber' => substr($faker->numerify('BE##########'), 0, 20),
                'companyname' => $faker->company(),
                'street' => $faker->streetName(),
                'streetnumber' => (string)$faker->buildingNumber(),
                'city_id' => $city->id,
                // Hardcode coordinates based on the city
                'lat' => match($city->name) {
                    'Brussels' => 50.8503 + $faker->randomFloat(3, -0.05, 0.05),
                    'Antwerp' => 51.2194 + $faker->randomFloat(3, -0.05, 0.05),
                    'Ghent' => 51.0543 + $faker->randomFloat(3, -0.05, 0.05),
                    'Bruges' => 51.2093 + $faker->randomFloat(3, -0.05, 0.05),
                    'Leuven' => 50.8798 + $faker->randomFloat(3, -0.05, 0.05),
                    default => $faker->latitude(50.5, 51.5),
                },
                'lng' => match($city->name) {
                    'Brussels' => 4.3517 + $faker->randomFloat(3, -0.05, 0.05),
                    'Antwerp' => 4.4025 + $faker->randomFloat(3, -0.05, 0.05),
                    'Ghent' => 3.7174 + $faker->randomFloat(3, -0.05, 0.05),
                    'Bruges' => 3.2247 + $faker->randomFloat(3, -0.05, 0.05),
                    'Leuven' => 4.7005 + $faker->randomFloat(3, -0.05, 0.05),
                    default => $faker->longitude(4.0, 5.0),
                },
            ]);
            
            // Attach 1-3 random categories
            $randomCategories = $categories->random(rand(1, 3))->pluck('id')->toArray();
            $teacher->category()->attach($randomCategories);
            
            // Skip creating availabilities for now
            
            if ($i % 10 === 0) {
                $this->command->info("Created {$i} teachers...");
            }
        }
        
        $this->command->info('All teachers created successfully!');
        
        // Index in Algolia
        $this->command->info('Importing teachers to Algolia...');
        \Artisan::call('scout:import', ['model' => "App\\Models\\Teacher"]);
        $this->command->info('Teachers imported to Algolia!');
    }
}
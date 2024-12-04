<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Specialization;
use Faker\Generator as Faker;
//use Faker\Provider\it_IT as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Faker $faker): void
    {

       // $specializationIds = Specialization::all()->pluck("id");

        for($i = 0; $i < 250; $i++) {
            $newUser = new User();
            $newUser->first_name = $faker->firstName();
            $newUser->last_name = $faker->lastName();
            //$newUser->specialization_id = $faker->randomElement($specializationIds);
            $newUser->password = $faker->password(6,20);
            $newUser->email = $faker->email();
            $newUser->home_address = $faker->streetAddress();
            $newUser->save();
        }
    }
}

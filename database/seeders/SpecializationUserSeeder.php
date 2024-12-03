<?php

namespace Database\Seeders;

use App\Models\Specialization;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;


class SpecializationUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Faker $faker): void
    {
        $users = User::all();

        $specializations = Specialization::all()->pluck("id");

        foreach($users as $user) {
            $user->specializations()->attach($faker->randomElements($specializations, rand(1,2)));
        }
    }
}

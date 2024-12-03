<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $table->id();
        $table->bigInteger('profile_id')->primary();
        $table->foreign('profile_id')->references('id')->on('profiles');
        $table->text('content');
        $table->string('email', 50);
        $table->string('first_name', 50);
        $table->string('last_name', 50);


    }
    $specializationIds = Specialization::all()->pluck("id");

    for($i = 0; $i < 250; $i++) {
        $newUser = new User();
        $newUser->specialization_id = $faker->randomElement($specializationIds);
        $newUser->first_name = $faker->firstName();
        $newUser->last_name = $faker->unique()->lastName();
        $newUser->email = $faker->email();
        $newUser->addres = $faker->streetAddress();
        $newUser->save();
    }
}

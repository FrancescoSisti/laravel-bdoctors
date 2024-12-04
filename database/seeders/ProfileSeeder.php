<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;


class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Faker $faker): void
    {
        $userIds = User::all()->pluck("id");

        foreach($userIds as $userId) {
            $newProfile = new Profile();
            $newProfile->user_id = $userId;
            $newProfile->curriculum = $faker->realTextBetween(200,1000);
            $newProfile->photo = $faker->imageUrl();
            $newProfile->office_address = $faker->streetAddress();
            $newProfile->phone = $faker->phoneNumber();
            $newProfile->services = $faker->realTextBetween(30,100);
            $newProfile->save();
        }
    }
}

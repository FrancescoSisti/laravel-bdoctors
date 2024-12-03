<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\Profile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;


class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Faker $faker): void
    {
        $profileIds = Profile::all()->pluck("id");


        foreach($profileIds as $profileId) {

            for($i = 0; $i < $faker->rand(1,3); $i++ ) {

                $newMessage = new Message();
                $newMessage->profile_id = $profileId;
                $newMessage->content = $faker->realTextBetween(50,200);
                $newMessage->email = $faker->email();
                $newMessage->first_name = $faker->firstName();
                $newMessage->last_name = $faker->unique()->lastName();
                $newMessage->save();
            }

        }
    }
}

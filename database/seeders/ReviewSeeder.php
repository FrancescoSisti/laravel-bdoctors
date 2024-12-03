<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\Review;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;


class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Faker $faker): void
    {
        $profileIds = Profile::all()->pluck("id");

        $newReview = new Review();
        $newReview->profile_id = $faker->randomElement($profileIds);
        $newReview->votes = $faker->rand(1,5);
        $newReview->content = $faker->realTextBetween(50,150);
        $newReview->email = $faker->email();
        $newReview->first_name = $faker->firstName();
        $newReview->last_name = $faker->lastName();
        $newReview->save();
    }
}

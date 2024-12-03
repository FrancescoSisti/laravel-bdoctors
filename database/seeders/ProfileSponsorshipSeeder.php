<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\Sponsorship;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;


class ProfileSponsorshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Faker $faker): void
    {
        $profiles = Profile::all();

        $sponsorships = Sponsorship::all()->pluck('id');

        foreach($profiles as $profile) {
            $randomSponsorship = $faker->randomElements($sponsorships->toArray());
            $profile->sponsorships()->attach($randomSponsorship, [
                'start_date' => $faker->date(),
                'end_date' => $faker->date()
            ]);
        }
    }
}
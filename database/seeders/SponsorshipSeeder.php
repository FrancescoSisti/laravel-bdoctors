<?php

namespace Database\Seeders;

use App\Models\Sponsorship;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;


class SponsorshipSeeder extends Seeder
{

    public function run(Faker $faker): void
    {
        $sponsorships = [
            [
                'name'=>'Bronze',
                'duration'=>24,
                'price'=>2,99,
                'description'=>''
            ],
            [
                'name'=>'Silver',
                'duration'=>48,
                'price'=>5,99,
                'description'=>''
            ],
            [
                'name'=>'Gold',
                'duration'=>144,
                'price'=>9,99,
                'description'=>''
            ]
        ];

        foreach($sponsorships as $singlesponsor) {
            $newSponsorship = new Sponsorship();
            $newSponsorship->name = $singlesponsor['name'];
            $newSponsorship->duration = $singlesponsor['duration'];
            $newSponsorship->price = $singlesponsor['price'];
            $newSponsorship->description = $faker->realTextBetween(20,80);
            $newSponsorship->save();
        }
    }
}

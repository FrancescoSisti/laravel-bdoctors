<?php

namespace Database\Seeders;

use App\Models\Profile;
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
            $newProfile->office_address = $faker->city();
            $newProfile->
        }
        $table->id();
        $table->bigInteger('user_id')->primary();
        $table->foreign('user_id')->references('id')->on('users');
        $table->text('curriculum')->nullable();
        $table->text('photo')->nullable();
        $table->string('office_address');
        $table->string('phone', 30)->nullable();
        $table->text('services')->nullable();
    }
}

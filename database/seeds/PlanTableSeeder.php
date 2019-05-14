<?php

use Illuminate\Database\Seeder;
use App\Plan;
class PlanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         //Plan::truncate();

        $faker = \Faker\Factory::create();

        // And now, let's create a few articles in our database:
        for ($i = 0; $i < 5; $i++) {
            Plan::create([
                'planName' => $faker->name,
                'frequency' => $faker->randomDigit,
				'fee' => $faker->randomDigit,
				'monthlyFee' => $faker->randomDigit ,
				'initiatonFee' => $faker->randomDigit ,
				'modDate' => $faker->date,
			    'modUser' => $faker->name,
            ]);
        }

    }
}

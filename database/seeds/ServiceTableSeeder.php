<?php

use Illuminate\Database\Seeder;

use App\Service;

class ServiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
          $faker = \Faker\Factory::create();
          for ($i = 0; $i < 5; $i++) {
            Service::create([
                'serviceName' => $faker->name,
                'serviceDesc' => 'hi it is test',
				'modDate' =>  $faker->date,
				'modUser' =>'un known' ,
				'created_at' => $faker->date
			   
            ]);
        }
    }
}

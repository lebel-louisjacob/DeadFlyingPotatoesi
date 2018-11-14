<?php

use App\SensorModel;
use Illuminate\Database\Seeder;

class SensorModelsTableSeeder extends Seeder
{

    public function run()
    {
        SensorModel::create([
            'name' => 'SDS011',
        ]);

        SensorModel::create([
            'name' => 'DHT22',
        ]);

        SensorModel::create([
            'name' => 'SDS021',
        ]);
    }
}

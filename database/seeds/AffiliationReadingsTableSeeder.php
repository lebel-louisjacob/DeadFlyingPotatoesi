<?php

use App\AffiliationReadings;
use Illuminate\Database\Seeder;

class AffiliationReadingsTableSeeder extends Seeder
{

    static $TABLE_NAME = 'affiliation_reading_types';

    public function run()
    {
        DB::table(AffiliationReadingsTableSeeder::$TABLE_NAME)->insert([
            [
                'sensor_model_id' => '2',
                'reading_type_id' => '1'
            ],
            [
                'sensor_model_id' => '1',
                'reading_type_id' => '2'
            ],
            [
                'sensor_model_id' => '3',
                'reading_type_id' => '3'
            ],
            [
                'sensor_model_id' => '2',
                'reading_type_id' => '4'
            ],
            [
                'sensor_model_id' => '1',
                'reading_type_id' => '3'
            ],
        ]);
    }
}
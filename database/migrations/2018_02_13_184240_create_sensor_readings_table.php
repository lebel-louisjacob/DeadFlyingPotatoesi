<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSensorReadingsTable extends Migration
{

    public function up()
    {
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->increments('id');
            $table->float('value');
            $table->unsignedInteger('sensor_id');
            $table->foreign('sensor_id')->references('id')->on('sensors');
            $table->string('type');
            $table->foreign('type')->references('type')->on('reading_types');
            //$table->unique(['sensor_id','created_at','type']);
            $table->double('latitude', 10, 7);
            $table->double('longitude', 10, 7);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sensor_readings');
    }
}

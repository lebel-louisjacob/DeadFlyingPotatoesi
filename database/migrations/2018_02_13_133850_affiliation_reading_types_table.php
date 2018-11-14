<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AffiliationReadingTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliation_reading_types', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sensor_model_id');
            $table->foreign('sensor_model_id')->references('id')->on('sensor_models');
            $table->unsignedInteger('reading_type_id');
            $table->foreign('reading_type_id')->references('id')->on('reading_types');
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
        Schema::dropIfExists('affiliation_reading_types');
    }
}

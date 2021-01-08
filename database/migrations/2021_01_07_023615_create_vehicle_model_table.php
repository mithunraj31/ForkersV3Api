<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleModelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_model', function (Blueprint $table) {
            $table->increments('id');
            $table->string('series_name')->nullable();
            $table->string('model_name');
            $table->string('power_type');
            $table->string('structural_method')->nullable();
            $table->string('engine_model')->nullable();
            $table->double('rated_load')->nullable(); // kg
            $table->double('fork_length')->nullable(); // mm
            $table->double('fork_width')->nullable(); // mm
            $table->double('standard_lift')->nullable(); // mm

            $table->double('maximum_lift')->nullable(); //mm
            $table->double('battery_voltage')->nullable(); // v
            $table->double('battery_capacity')->nullable(); // AH

            $table->double('fuel_tank_capcity')->nullable(); // L
            $table->double('body_weight'); // kg
            $table->double('body_length'); // mm
            $table->double('body_width'); // mm

            $table->double('head_guard_height'); // mm
            $table->double('min_turning_radius'); // mm

            $table->double('ref_load_center'); // mm

            $table->string('tire_size_front_wheel');
            $table->string('tire_size_rear_wheel');

            $table->longText('remarks');
            $table->timestamps();

            $table->unsignedBigInteger('owner_id')->nullable();
            $table->unsignedInteger('manufacturer_id');

            $table->foreign('owner_id')->references('id')->on('users');
            $table->foreign('manufacturer_id')->references('id')->on('manufacturer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_model');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVehicleProfileColumnsToVehicle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehicle', function (Blueprint $table) {
            $table->string('vehicle_number');
            $table->string('structural_method')->nullable();
            $table->string('power_type')->nullable();
            $table->double('rated_load')->nullable(); // kg
            $table->double('fork_length')->nullable(); // mm
            $table->double('standard_lift')->nullable(); // mm

            $table->double('maximum_lift')->nullable(); // mm
            $table->double('battery_voltage')->nullable(); // v
            $table->double('battery_capacity')->nullable(); // AH
            $table->double('hour_meter_initial_value')->nullable();
            $table->double('operating_time')->nullable();
            $table->double('cumulative_uptime')->nullable();


            $table->datetime('introduction_date');
            $table->integer('contract')->nullable(); // 資産 or リース or レンタル
            $table->string('key_number')->nullable();
            $table->text('installation_location')->nullable();

            $table->text('option1')->nullable();
            $table->text('option2')->nullable();
            $table->text('option3')->nullable();
            $table->text('option4')->nullable();
            $table->text('option5')->nullable();

            $table->longText('remarks')->nullable();

            $table->unsignedInteger('model_id')->nullable();
            $table->foreign('model_id')->references('id')->on('vehicle_model');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicle', function (Blueprint $table) {
            //
        });
    }
}

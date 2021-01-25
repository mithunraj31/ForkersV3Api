<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOperatorIdAndVehicleIdToEvent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event', function (Blueprint $table) {
            $table->unsignedBigInteger('operator_id');
            $table->unsignedBigInteger('vehicle_id');

            $table->foreign('operator_id')->references('id')->on('operator');
            $table->foreign('vehicle_id')->references('id')->on('vehicle');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event', function (Blueprint $table) {
            //
        });
    }
}

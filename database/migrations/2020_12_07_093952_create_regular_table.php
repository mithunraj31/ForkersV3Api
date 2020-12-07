<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegularTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regular', function (Blueprint $table) {
            $table->id();
            $table->string('device_id');
            $table->string('username');
            $table->datetime('time');
            $table->integer('type');
            $table->string('driver_id');
            $table->integer('status');
            $table->float('direction');
            $table->float('speed');
            $table->float('latitude');
            $table->float('longitude');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('regular');
    }
}

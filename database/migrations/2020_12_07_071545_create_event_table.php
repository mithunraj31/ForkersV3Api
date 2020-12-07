<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event', function (Blueprint $table) {
            $table->id();
            $table->string('event_id');
            $table->string('device_id');
            $table->string('driver_id');
            $table->integer('type');
            $table->float('latitude');
            $table->float('longitude');
            $table->float('gx');
            $table->float('gy');
            $table->float('gz');
            $table->float('roll');
            $table->float('pitch');
            $table->float('yaw');
            $table->integer('status');
            $table->float('direction');
            $table->float('speed');
            $table->string('video_id');
            $table->datetime('time');
            $table->string('username');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event');
    }
}

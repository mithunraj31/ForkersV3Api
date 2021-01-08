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
            $table->increments('id');
            $table->uuid('event_id', 128);
            $table->string('device_id', 45);
            $table->string('driver_id', 45)->nullable();
            $table->integer('type');
            $table->float('latitude')->nullable();
            $table->float('longitude')->nullable();
            $table->float('gx')->nullable();
            $table->float('gy')->nullable();
            $table->float('gz')->nullable();
            $table->float('roll')->nullable();
            $table->float('pitch')->nullable();
            $table->float('yaw')->nullable();
            $table->integer('status')->nullable();
            $table->float('direction')->nullable();
            $table->float('speed')->nullable();
            $table->string('video_id', 128)->nullable();
            $table->datetime('time')->nullable();
            $table->string('username')->nullable();
            $table->softDeletes();
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

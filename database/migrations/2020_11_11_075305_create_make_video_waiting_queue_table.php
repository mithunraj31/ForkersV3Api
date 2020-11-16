<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMakeVideoWaitingQueueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('make_video_waiting_queue', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->nullable(false);
            $table->string('event_id')->unique();
            $table->dateTime('begin_datetime')->nullable(false);
            $table->dateTime('end_datetime')->nullable(false);
            $table->integer('duration')->nullable(false);
            $table->string('username')->nullable(false);
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
        Schema::dropIfExists('make_video_waiting_queue');
    }
}

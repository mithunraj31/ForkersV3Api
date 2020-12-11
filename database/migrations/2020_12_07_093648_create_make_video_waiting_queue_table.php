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
            $table->timestamps();
            $table->string('device_id');
            $table->datetime('begin_datetime');
            $table->datetime('end_datetime');
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
        Schema::dropIfExists('make_video_waiting_queue');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveEventIdAndDurationTimeFromMakeVideoWaitingQueueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('make_video_waiting_queue', function (Blueprint $table) {
            $table->dropColumn('event_id');
            $table->dropColumn('duration');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('make_video_waiting_queue', function (Blueprint $table) {

        });
    }
}

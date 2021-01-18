<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device', function (Blueprint $table) {
            $table->string('device_id')->primary();
            $table->string('plate_number');
            $table->string('scan_code');
            $table->integer('channel_number');
            $table->string('group_name');
            $table->string('tcp_server_addr');
            $table->integer('tcp_stream_out_port');
            $table->string('udp_server_addr');
            $table->integer('udp_stream_out_port');
            $table->integer('net_type');
            $table->string('device_type');
            $table->dateTime('create_time');
            $table->dateTime('update_time');
            $table->tinyInteger('is_active');
            $table->string('stk_user');
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
        Schema::dropIfExists('device');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drive', function (Blueprint $table) {
            $table->id();
            $table->string('device_id', 45)->nullable();
            $table->string('user_name', 45)->nullable();
            $table->datetime('time')->nullable();
            $table->integer('type')->nullable();
            $table->string('driver_id', 45)->nullable();
            $table->integer('status')->nullable();
            $table->float('direction')->nullable();
            $table->float('speed')->nullable();
            $table->float('latitude')->nullable();
            $table->float('longitude')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drive');
    }
}

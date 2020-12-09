<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCameraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('camera', function (Blueprint $table) {
            $table->increments('id');
            $table->string('device_id');
            $table->integer('rotation')->default('0');
            $table->string('ch');
        });

        Schema::table('camera', function (Blueprint $table) {
            $table->string('device_id', 50)->nullable()->change();
            $table->string('ch', 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('camera');
    }
}

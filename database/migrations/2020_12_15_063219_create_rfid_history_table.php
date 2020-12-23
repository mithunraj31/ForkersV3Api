<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRfidHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rfid_history', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('rfid', 45);
            $table->string('operator_id');
            $table->dateTime('assigned_from');
            $table->dateTime('assigned_till')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rfid_history');
    }
}

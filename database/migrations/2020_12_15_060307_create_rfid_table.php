<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRfidTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rfid', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('rfid', 45)->unique();
            $table->string('created_by');
            $table->integer('customer_id');
            $table->integer('current_operator_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rfid');
    }
}

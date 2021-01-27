<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperatorStatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operator_stat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('operator_id');
            $table->timestamp('date');
            $table->integer('duration');
            $table->timestamps();

            $table->foreign('operator_id')->references('id')->on('operator');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operator_stat');
    }
}

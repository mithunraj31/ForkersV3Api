<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->text('api_path');
            $table->boolean('is_private');
            $table->timestamps();
            $table->unsignedBigInteger('owner_id');
            $table->unsignedInteger('customer_id');
            $table->foreign('owner_id')->references('id')->on('users');
            $table->foreign('customer_id')->references('id')->on('customer');
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
        Schema::dropIfExists('charts');
    }
}

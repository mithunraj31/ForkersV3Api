<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver', function (Blueprint $table) {
            $table->id();
            $table->string('driver_id');
            $table->timestamps();
            $table->string('name');
            $table->date('dob');
            $table->longText('address');
            $table->string('license_no')->unique();
            $table->date('license_received_date');
            $table->date('license_renewal_date');
            $table->longText('license_location');
            $table->string('phone_no');
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
        Schema::dropIfExists('driver');
    }
}

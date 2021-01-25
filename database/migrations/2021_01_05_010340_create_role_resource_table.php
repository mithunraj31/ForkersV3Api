<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleResourceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_resource', function (Blueprint $table) {
            $table->id();
            $table->boolean('edit');
            $table->boolean('add');
            $table->boolean('view');
            $table->boolean('delete');

            $table->timestamps();

            $table->string('resource');
            $table->bigInteger('role_id');
            $table->bigInteger('owner_id')->nullable();
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
        Schema::dropIfExists('role_resource');
    }
}

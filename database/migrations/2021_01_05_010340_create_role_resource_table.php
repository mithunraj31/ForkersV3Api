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
            $table->boolean('edit')->nullable(false);
            $table->boolean('add')->nullable(false);
            $table->boolean('view')->nullable(false);
            $table->boolean('delete')->nullable(false);

            $table->timestamps();

            $table->string('resource')->nullable(false);
            $table->bigInteger('role_id')->nullable(false);
            $table->bigInteger('owner_id')->nullable(true);
            $table->datetime('deleted_at')->nullable(true);
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

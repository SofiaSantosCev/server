<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    protected $table = 'users';

    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
           $table->increments('id');
            $table->string('name')->nullable(false);
            $table->integer('rol_id')->nullable(false);
            $table->string('email')->unique()->nullable(false);
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

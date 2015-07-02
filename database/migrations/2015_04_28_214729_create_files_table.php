<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFilesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url', 255)->unique();
            $table->string('short_url', 127)->unique();
            $table->text('local_path');
            $table->text('filename');
            $table->integer('time');
            $table->text('password')->nullable();
            $table->timestamps();
        });

        DB::statement('CREATE UNIQUE INDEX local_path ON files (local_path (100))');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('files');
    }
}
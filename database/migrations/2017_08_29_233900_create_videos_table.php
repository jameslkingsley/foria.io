<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('name');
            $table->longText('key');
            $table->longText('path')->nullable();
            $table->longText('transcoder_id')->nullable();
            $table->bigInteger('duration')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('frame_rate')->nullable();
            $table->boolean('subscriber_only')->default(false);
            $table->bigInteger('token_price')->default(0);
            $table->boolean('has_processed')->default(false);
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
        Schema::dropIfExists('videos');
    }
}

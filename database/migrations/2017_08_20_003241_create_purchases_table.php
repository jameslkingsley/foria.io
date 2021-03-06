<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('payee_id')->nullable();
            $table->string('stripe_id')->nullable();
            $table->morphs('model');
            $table->string('name');
            $table->boolean('via_tokens')->default(true);
            $table->bigInteger('tokens')->unsigned()->nullable();
            $table->bigInteger('amount')->unsigned()->nullable();
            $table->bigInteger('payout')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('payee_id')->references('id')->on('users')->onDelete('cascade');
            // $table->unique(['user_id', 'model_id', 'model_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
    }
}

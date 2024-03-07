<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('places', function (Blueprint $table) {
            $table->id('id');
            $table->string('name', 128);
            $table->text('description');
            $table->string('latitude', 32)->nullable();
            $table->string('longitude', 32)->nullable();
            $table->string('map_link', 256)->nullable();
            $table->tinyInteger('order_num')->unsigned()->default(0);
            $table->tinyInteger('visible')->unsigned()->default(1);
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });

        Schema::create('attraction_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->string('name', 256);
            $table->tinyInteger('order_num')->unsigned()->default(0);
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->timestamps();
            
            $table->foreign('parent_id')->references('id')->on('attraction_categories')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });

        Schema::create('attractions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128);
            $table->integer('attraction_category_id')->unsigned()->nullable();
            $table->string('title', 256)->nullable();
            $table->string('subtitle', 256)->nullable();
            $table->text('content')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('place_id')->unsigned()->nullable();
            $table->string('latitude', 32)->nullable();
            $table->string('longitude', 32)->nullable();
            $table->string('map_link', 256)->nullable();
            $table->tinyInteger('approved')->unsigned()->default(1);
            $table->tinyInteger('visible')->unsigned()->default(1);
            $table->tinyInteger('stand_out')->unsigned()->default(0);
            $table->string('note', 255)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->foreign('place_id')->references('id')->on('places')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->foreign('attraction_category_id')->references('id')->on('attraction_categories')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attractions');
        Schema::dropIfExists('attraction_categories');
        Schema::dropIfExists('places');
    }
};

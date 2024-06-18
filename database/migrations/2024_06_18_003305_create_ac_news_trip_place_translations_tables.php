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
        Schema::create('trip_translations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('trip_id')->unsigned();
            $table->integer('language_id')->unsigned();
            $table->string('lang_code', 32);
            $table->string('title', 256)->nullable();
            $table->string('subtitle', 256)->nullable();
            $table->string('summary', 512)->nullable();
            $table->text('text')->nullable();
            $table->string('slug', 128)->nullable();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->tinyInteger('approved')->unsigned()->default(1);
            $table->timestamps();

            $table->foreign('trip_id')->references('id')->on('trips')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('language_id')->references('id')->on('languages')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('created_by')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->foreign('updated_by')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });

        Schema::create('place_translations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('place_id')->unsigned();
            $table->integer('language_id')->unsigned();
            $table->string('lang_code', 32);
            $table->string('name', 128);
            $table->text('description')->nullable();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->tinyInteger('approved')->unsigned()->default(1);
            $table->timestamps();

            $table->foreign('place_id')->references('id')->on('places')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('language_id')->references('id')->on('languages')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('created_by')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->foreign('updated_by')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });

        Schema::create('attraction_category_translations', function (Blueprint $table) {
            $table->id();
            $table->integer('attraction_category_id')->unsigned();
            $table->integer('language_id')->unsigned();
            $table->string('lang_code', 32);
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->string('name', 256);
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->tinyInteger('approved')->unsigned()->default(1);
            $table->timestamps();

            $table->foreign('attraction_category_id')->references('id')->on('attraction_categories')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('language_id')->references('id')->on('languages')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('created_by')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->foreign('updated_by')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });

        Schema::create('news_translations', function (Blueprint $table) {
            $table->id('id');
            $table->bigInteger('news_id')->unsigned();
            $table->integer('language_id')->unsigned();
            $table->string('lang_code', 32);
            $table->string('title', 256);
            $table->string('subtitle', 256)->nullable();
            $table->string('slug', 128)->nullable();
            $table->text('summary')->nullable();
            $table->text('text');
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->tinyInteger('approved')->unsigned()->default(1);
            $table->timestamps();

            $table->foreign('news_id')->references('id')->on('news')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('language_id')->references('id')->on('languages')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('created_by')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->foreign('updated_by')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });

        Schema::create('news_category_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('news_category_id')->nullable();
            $table->integer('language_id')->unsigned();
            $table->string('lang_code', 32);
            $table->string('name', 256);
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->tinyInteger('approved')->unsigned()->default(1);
            $table->timestamps();

            $table->foreign('news_category_id')->references('id')->on('news_categories')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('language_id')->references('id')->on('languages')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('created_by')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->foreign('updated_by')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('trip_translations');
        Schema::dropIfExists('place_translations');
        Schema::dropIfExists('attraction_category_translations');
    }
};

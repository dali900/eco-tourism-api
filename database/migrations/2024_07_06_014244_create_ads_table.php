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
        Schema::create('ad_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->string('name', 256);
            $table->tinyInteger('order_num')->unsigned()->default(0);
            $table->tinyInteger('visible')->unsigned()->default(0);
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->timestamps();
            
            $table->foreign('parent_id')->references('id')->on('ad_categories')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->foreign('updated_by')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });

        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id')->unsigned()->nullable();
            $table->string('title', 256);
            $table->text('description')->nullable();
            $table->bigInteger('price')->unsigned()->nullable();
            $table->string('currency', 16)->nullable();
            $table->string('slug', 128)->nullable();
            $table->bigInteger('place_id')->unsigned()->nullable();
            $table->tinyInteger('approved')->unsigned()->default(1);
            $table->dateTime('published_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->string('note', 512)->nullable();
            $table->tinyInteger('suggested')->unsigned()->nullable();
            $table->tinyInteger('order_num')->unsigned()->nullable();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->foreign('updated_by')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->foreign('place_id')->references('id')->on('places')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->foreign('category_id')->references('id')->on('ad_categories')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });

        Schema::create('ad_category_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ad_category_id')->nullable();
            $table->integer('language_id')->unsigned();
            $table->string('lang_code', 32);
            $table->string('name', 256);
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->tinyInteger('approved')->unsigned()->default(1);
            $table->timestamps();

            $table->foreign('ad_category_id')->references('id')->on('ad_categories')
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

        Schema::create('ad_translations', function (Blueprint $table) {
            $table->id('id');
            $table->bigInteger('ad_id')->unsigned();
            $table->integer('language_id')->unsigned();
            $table->string('lang_code', 32);
            $table->string('title', 256);
            $table->text('description')->nullable();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->tinyInteger('approved')->unsigned()->default(1);
            $table->timestamps();

            $table->foreign('ad_id')->references('id')->on('ads')
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
        Schema::dropIfExists('ad_translations');
        Schema::dropIfExists('ad_category_translations');
        Schema::dropIfExists('ads');
        Schema::dropIfExists('ad_categories');
    }
};

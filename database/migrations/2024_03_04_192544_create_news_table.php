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
        Schema::create('news_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->string('name', 256);
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->timestamps();
            
            $table->foreign('parent_id')->references('id')->on('news_categories')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });

        Schema::create('news', function (Blueprint $table) {
            $table->id('id');
            $table->string('title', 256);
            $table->integer('news_category_id')->unsigned()->nullable();
            $table->string('subtitle', 256);
            $table->text('summary');
            $table->text('text');
            $table->dateTime('publish_date');
            $table->tinyInteger('approved')->default(1);
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->foreign('news_category_id')->references('id')->on('news_categories')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
        Schema::dropIfExists('news_categories');
    }
};

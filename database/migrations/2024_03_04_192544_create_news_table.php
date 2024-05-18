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
            $table->tinyInteger('order_num')->unsigned()->default(0);
            $table->tinyInteger('visible')->default(1);
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->timestamps();
            
            $table->foreign('parent_id')->references('id')->on('news_categories')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->foreign('updated_by')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });

        Schema::create('news', function (Blueprint $table) {
            $table->id('id');
            $table->string('title', 256);
            $table->string('subtitle', 256)->nullable();
            $table->string('slug', 128)->nullable();
            $table->text('summary')->nullable();
            $table->text('text');
            $table->dateTime('publish_date')->nullable();
            $table->tinyInteger('approved')->default(1);
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->foreign('updated_by')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
        
        Schema::create('news_news_category', function (Blueprint $table) {
            $table->id('id');
            $table->bigInteger('news_id')->unsigned()->nullable();
            $table->unsignedInteger('news_category_id')->nullable();
            $table->timestamps();

            $table->foreign('news_id')->references('id')->on('news')->onDelete('cascade');
            $table->foreign('news_category_id')->references('id')->on('news_categories')->onDelete('cascade');
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

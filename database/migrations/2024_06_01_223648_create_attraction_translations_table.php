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
        Schema::create('languages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 128);
            $table->string('lang_code', 32);
            $table->string('translated_name', 128)->nullable();
            $table->string('note', 64)->nullable();
            $table->string('translated_note', 64)->nullable();
            $table->tinyInteger('visible')->unsigned()->default(0);
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

        Schema::create('attraction_translations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('attraction_id')->unsigned();
            $table->integer('language_id')->unsigned();
            $table->string('lang_code', 32);
            $table->string('name', 128);
            $table->string('title', 256)->nullable();
            $table->string('subtitle', 256)->nullable();
            $table->string('summary', 512)->nullable();
            $table->text('content')->nullable();
            $table->string('slug', 128)->nullable();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->tinyInteger('approved')->unsigned()->default(1);
            $table->timestamps();

            $table->foreign('attraction_id')->references('id')->on('attractions')
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
        Schema::dropIfExists('languages');
        Schema::dropIfExists('attraction_translations');
    }
};

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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('original_name', 640);
            $table->integer('file_model_id')->unsigned();
            $table->string('file_model_type', 256);
            $table->string('file_path', 640);
            $table->string('ext', 16)->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('file_tag', 256)->nullable();
            $table->boolean('is_tmp')->nullable();
            $table->boolean('is_public')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
				->onDelete('set null')
				->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};

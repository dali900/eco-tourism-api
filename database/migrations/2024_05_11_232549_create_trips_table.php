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
        Schema::create('trips', function (Blueprint $table) {
            $table->id('id');
            $table->string('title', 256);
            $table->string('subtitle', 256);
            $table->string('slug', 128)->nullable();
            $table->text('summary');
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

        Schema::create('trip_attraction', function (Blueprint $table) {
            $table->id('id');
            $table->bigInteger('trip_id')->unsigned()->nullable();
            $table->bigInteger('attraction_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');
            $table->foreign('attraction_id')->references('id')->on('attractions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
        Schema::dropIfExists('trip_attraction');
    }
};

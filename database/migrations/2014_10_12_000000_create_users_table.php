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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('role', 128)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('first_name', 128)->nullable();
            $table->string('last_name', 128)->nullable();
            $table->string('status', 64)->nullable();
            $table->boolean('active')->default(1)->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('note', 255)->nullable()->nullable();
            $table->dateTime('last_login')->nullable();
            $table->string('ip', 32)->nullable();

            $table->foreign('user_id')->references('id')->on('users')
				->onDelete('set null')
				->onUpdate('cascade');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 128);
            $table->string('key', 192);
            $table->string('interval', 128);
            $table->integer('amount')->unsigned()->nullable();
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

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->integer('subscription_plan_id')->unsigned();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('status', 64)->default('active');
            $table->string('interval', 128);
            $table->dateTime('start_date')->nullable()->useCurrent();
            $table->dateTime('end_date')->nullable();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->string('note')->nullable();
            $table->timestamps();

            $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->foreign('created_by')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->foreign('updated_by')->references('id')->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });

        Schema::create('free_trial_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 128);
            $table->string('key', 192);
            $table->smallInteger('days')->unsigned();
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

        Schema::create('free_trials', function (Blueprint $table) {
            $table->id();
            $table->integer('free_trial_plan_id')->unsigned();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('status', 64)->default('active');
            $table->dateTime('start_date')->nullable()->useCurrent();
            $table->dateTime('end_date')->nullable();
            $table->string('note')->nullable();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('free_trial_plan_id')->references('id')->on('free_trial_plans')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('set null')
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
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_plans');
        Schema::dropIfExists('free_trials');
        Schema::dropIfExists('free_trial_plans');
    }
};

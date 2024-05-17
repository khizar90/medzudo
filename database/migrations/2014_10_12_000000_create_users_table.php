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
            $table->uuid()->primary();
            $table->string('first_name');
            $table->string('last_name')->default('');
            $table->string('type')->default('');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('image')->default('');
            $table->string('account_type');
            $table->string('password');
            $table->string('position')->default('');
            $table->string('location')->default('');
            $table->string('request_image')->default('');
            $table->boolean('request_verify')->default(0);
            $table->string('lat')->default('');
            $table->string('long')->default('');
            $table->boolean('verify')->default(0);
            $table->longText('about')->default('');
            $table->string('carrier')->default('');
            $table->string('phone_number')->default('');
            $table->string('for_training')->default('');
            $table->integer('no_of_bed')->default(0);
            $table->string('special_feature')->default('');
            $table->integer('No_of_employees')->default(0);
            $table->string('multi_images')->default('');
            $table->string('website_link')->default('');
            $table->string('linkedin_link')->default('');
            $table->string('instagram_link')->default('');
            $table->string('facebook_link')->default('');
            $table->string('youtube_link')->default('');
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

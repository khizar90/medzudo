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
            $table->string('sector')->default('');
            $table->string('image')->default('');
            $table->string('account_type');
            $table->string('password');
            $table->string('position')->default('');
            $table->string('experience')->default('');
            $table->string('age')->default('');
            $table->string('gender')->default('');
            $table->string('location')->default('');
            $table->string('lat')->default('');
            $table->string('lng')->default('');
            $table->string('professionId')->default('');
            $table->string('professionName')->default('');
            $table->string('specializationId')->default('');
            $table->string('specializationName')->default('');
            $table->string('subSpecializationId')->default('');
            $table->string('subSpecializationName')->default('');
            $table->string('departmentId')->default('');
            $table->string('departmentName')->default('');
            $table->string('trainingId')->default('');
            $table->string('trainingName')->default('');
            $table->string('request_image')->default('');
            $table->boolean('request_verify')->default(0);
            $table->boolean('verify')->default(0);
            $table->longText('about')->default('');
            $table->string('carrier')->default('');
            $table->string('phone_number')->default('');
            $table->string('for_training')->default('');
            $table->string('no_of_bed')->default('0');
            $table->string('special_feature')->default('');
            $table->string('no_of_employe')->default('0');
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

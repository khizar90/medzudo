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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->references('uuid')->on('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('media');
            $table->string('title');
            $table->string('organizer_name');
            $table->string('start_date');
            $table->string('start_time')->default('');
            $table->string('start_timestamp')->default('');
            $table->string('end_date');
            $table->string('end_time')->default('');
            $table->string('end_timestamp')->default('');
            $table->string('location')->default('');
            $table->string('lat')->default('');
            $table->string('lng')->default('');
            $table->string('address')->default('');
            $table->string('website')->default('');
            $table->string('registration_link')->default('');
            $table->string('availability');
            $table->string('audience_limit')->default('');
            $table->string('password')->default('');
            $table->longText('description');
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

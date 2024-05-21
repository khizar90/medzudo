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
        Schema::create('communities', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->references('uuid')->on('users')->onDelete('cascade');
            $table->string('cover');
            $table->string('logo');
            $table->string('name');
            $table->string('tagline');
            $table->string('location');
            $table->string('lat');
            $table->string('lng');
            $table->string('categories');
            $table->string('type');
            $table->string('mode');
            $table->string('price');
            $table->longText('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communities');
    }
};

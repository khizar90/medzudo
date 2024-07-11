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
        Schema::create('community_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->references('uuid')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('community_id');
            $table->longText('media')->default('');
            $table->string('thumbnail')->default('');
            $table->longText('caption')->default('');
            $table->string('option_1')->default('');
            $table->string('option_2')->default('');
            $table->string('option_3')->default('');
            $table->string('option_4')->default('');
            $table->string('type');
            $table->string('size')->default('0.8');
            $table->string('time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_posts');
    }
};

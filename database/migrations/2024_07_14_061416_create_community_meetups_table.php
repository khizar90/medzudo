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
        Schema::create('community_meetups', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->references('uuid')->on('users')->onDelete('cascade');
            $table->foreignId('community_id')->constrained('communities')->onDelete('cascade');
            $table->string('cover');
            $table->string('title');
            $table->string('organizer');
            $table->string('category');
            $table->string('start_date');
            $table->string('start_time')->default('');
            $table->string('start_timestamp')->default('');
            $table->string('end_date');
            $table->string('end_time')->default('');
            $table->string('end_timestamp')->default('');
            $table->string('location')->default('');
            $table->string('lat')->default('');
            $table->string('lng')->default('');
            $table->longText('address')->default('');
            $table->string('website')->default('');
            $table->string('register_link')->default('');
            $table->string('mode');
            $table->string('audience_limit')->default('');
            $table->string('password')->default('');
            $table->string('price')->default('');
            $table->string('cme_point')->default('');
            $table->longText('description');
            $table->longText('file')->default('');
            $table->integer('status')->default(0);
            $table->longText('host_url');
            $table->longText('viewer_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_meetups');
    }
};

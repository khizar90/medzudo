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
        Schema::create('community_course_section_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('community_course_sections')->onDelete('cascade');
            $table->text('video');
            $table->text('thumbnail')->default('');
            $table->string('title');
            $table->longText('description');
            $table->string('duration');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_course_section_videos');
    }
};

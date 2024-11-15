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
        Schema::table('management', function (Blueprint $table) {
            $table->foreignUuid('management_id')->references('uuid')->on('users')->onDelete('cascade')->after('user_id');
            $table->boolean('is_mention')->default(0)->after('designation');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('management', function (Blueprint $table) {
            $table->dropColumn('management_id');
        });
    }
};

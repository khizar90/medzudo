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
        Schema::table('user_business_details', function (Blueprint $table) {
            $table->string('companyFeatureId')->default('')->after('medicalFocusName');
            $table->string('companyFeatureName')->default('')->after('companyFeatureId');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_business_details', function (Blueprint $table) {
            $table->dropColumn('companyFeatureId');
            $table->dropColumn('companyFeatureName');
        });
    }
};

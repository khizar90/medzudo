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
        Schema::create('user_business_details', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->references('uuid')->on('users')->onDelete('cascade');
            $table->string('institution_number')->default('');
            $table->string('trainingFocusId')->default('');
            $table->string('trainingFocusName')->default('');
            $table->string('trainingQualificationId')->default('');
            $table->string('trainingQualificationName')->default('');
            $table->string('staffBenefitId')->default('');
            $table->string('staffBenefitName')->default('');
            $table->string('specialFeatureId')->default('');
            $table->string('specialFeatureName')->default('');
            $table->string('treatmentServiceId')->default('');
            $table->string('treatmentServiceName')->default('');
            $table->string('legalTypeId')->default('');
            $table->string('legalTypeName')->default('');
            $table->string('yearlyRevenueId')->default('');
            $table->string('yearlyRevenueName')->default('');
            $table->string('financingStageId')->default('');
            $table->string('financingStageName')->default('');
            $table->longText('customer_problem');
            $table->longText('business_model');
            $table->longText('market_description');
            $table->longText('customer_focus');
            $table->longText('technology_description');
            $table->longText('usp');
            $table->string('targetGroupId')->default('');
            $table->string('targetGroupName')->default('');
            $table->string('medicalFocusId')->default('');
            $table->string('medicalFocusName')->default('');
            $table->string('pitch_deck')->default('');
            $table->longText('member_benefits');
            $table->longText('working_groups');
            $table->longText('association_engagement');
            $table->longText('member_fee');
            $table->string('become_member')->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_business_details');
    }
};

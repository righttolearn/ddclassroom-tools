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
        Schema::create('monthly_attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedSmallInteger('sub_grade_id');
            $table->unsignedSmallInteger('year')->index();
            $table->unsignedTinyInteger('month_id')->index();
            $table->smallInteger('total_hours')->default(0);
            $table->smallInteger('total_absences')->default(0);
            $table->decimal('absence_percentage', 5,2)->default(0);
            $table->enum('support_type', ['cash', 'credit_card'])->nullable();
            $table->boolean('is_eligible_for_support')->default(false);
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->unsignedBigInteger('user_id');

            // Foreign Keys
            $table->foreign('month_id')
                ->references('id')
                ->on('months');

            $table->foreign('sub_grade_id')
                ->references('id')
                ->on('sub_grades');

            $table->foreign('student_id')
                ->references('id')
                ->on('students');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->unique(['student_id', 'year', 'month_id']);

            $table->index(['sub_grade_id', 'support_type']);
            $table->index(['year', 'month_id', 'absence_percentage']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_attendance_logs');
    }
};

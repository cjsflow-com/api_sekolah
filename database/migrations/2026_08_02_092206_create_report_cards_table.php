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
        Schema::create('report_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->foreignId('school_class_id')->constrained('school_classes')->onDelete('cascade');
            $table->decimal('average_score', 5, 2)->nullable();
            $table->unsignedInteger('attendance_present')->nullable();
            $table->unsignedInteger('attendance_sick')->nullable();
            $table->unsignedInteger('attendance_permission')->nullable();
            $table->unsignedInteger('attendance_absent')->nullable();
            $table->text('teacher_notes')->nullable();
            $table->text('principal_notes')->nullable();
            $table->boolean('is_finalized')->default(false);
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_cards');
    }
};

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
        Schema::table('school_classes', function (Blueprint $table) {
            //
            $table->foreignId('education_unit_id')
                ->after('academic_year_id')
                ->constrained('education_units')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->dropColumn('major');
            $table->index([
                'education_unit_id',
                'academic_year_id',
                'level',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_classes', function (Blueprint $table) {
            $table->dropIndex([
                'education_unit_id',
                'academic_year_id',
                'level',
            ]);

            $table->dropForeign(['education_unit_id']);
            $table->dropColumn('education_unit_id');

            $table->string('major')->nullable();
        });
    }
};

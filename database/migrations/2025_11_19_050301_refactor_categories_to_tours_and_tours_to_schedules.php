<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Refactoring:
     * - categories → tours (thông tin chung)
     * - tours → tour_schedules (lịch trình cụ thể)
     */
    public function up(): void
    {
        // Step 1: Add location column to categories
        if (!Schema::hasColumn('categories', 'location')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('location')->nullable()->after('description');
            });
        }

        // Step 2: Copy location from tours to categories
        DB::update('UPDATE categories c
                    INNER JOIN tours t ON c.id = t.category_id
                    SET c.location = t.location
                    WHERE t.location IS NOT NULL AND c.location IS NULL');

        // Step 3: Drop foreign keys that will be affected
        Schema::table('tour_schedules', function (Blueprint $table) {
            $table->dropForeign(['tour_id']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['tour_id']);
        });

        Schema::table('tours', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        // Step 4: Update tour_schedules: map tour_id to category_id
        // tour_schedules.tour_id (old) → should point to categories.id (new tours)
        DB::update('UPDATE tour_schedules ts
                    INNER JOIN tours t ON ts.tour_id = t.id
                    SET ts.tour_id = t.category_id');

        // Step 5: Update reviews: map tour_id to category_id  
        DB::update('UPDATE reviews r
                    INNER JOIN tours t ON r.tour_id = t.id
                    SET r.tour_id = t.category_id');

        // Step 6: Rename old tours table first (before renaming categories)
        Schema::rename('tours', 'tours_old');

        // Step 7: Rename categories to tours (final)
        Schema::rename('categories', 'tours');

        // Step 9: Recreate foreign keys pointing to new tours table
        Schema::table('tour_schedules', function (Blueprint $table) {
            $table->foreign('tour_id')->references('id')->on('tours')->onDelete('cascade');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign('tour_id')->references('id')->on('tours')->onDelete('cascade');
        });

        // Step 10: Drop old tours table (no longer needed)
        // But first check if there's any data we need to preserve
        Schema::dropIfExists('tours_old');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['tour_id']);
        });

        Schema::table('tour_schedules', function (Blueprint $table) {
            $table->dropForeign(['tour_id']);
        });

        // Rename back
        Schema::rename('tours', 'categories');
        
        // Remove location column
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('location');
        });

        // Note: Full rollback would require recreating the old tours table
        // and remapping all foreign key relationships
    }
};

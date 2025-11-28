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
     * Updates reviews table to:
     * - Ensure rating is integer (1-5) to match application validation
     * - Add admin_reply column for admin responses to reviews
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Ensure rating column is integer (1-5) to match application validation
            $table->unsignedTinyInteger('rating')->change()->comment('Rating value should be between 1 and 5');
            
            // Add admin_reply column for admin responses (similar to App Store/Google Play)
            $table->text('admin_reply')->nullable()->after('content');
            $table->timestamp('admin_replied_at')->nullable()->after('admin_reply');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Revert rating back to integer
            $table->unsignedTinyInteger('rating')->change()->comment('Rating value should be between 1 and 5');
            
            // Remove admin reply columns
            $table->dropColumn(['admin_reply', 'admin_replied_at']);
        });
    }
};

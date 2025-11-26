<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    /**
     * This migration originally attempted to swap the meaning of the
     * `categories`, `tours`, and `tour_schedules` tables which removed the
     * ability to manage tour categories from the admin area. We now guard
     * against that destructive behaviour by making sure the canonical tables
     * exist with the expected columns so fresh installs – and environments that
     * accidentally ran the broken migration – end up with the ERD structure
     * shown in the requirements.
     */
    public function up(): void
    {
        $this->ensureCategoriesTable();
        $this->ensureToursTable();
    }

    public function down(): void
    {
        // No destructive rollback on purpose – we only ensure structure exists.
    }

    private function ensureCategoriesTable(): void
    {
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('image_url')->nullable();
                $table->timestamps();
            });
            return;
        }

        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'image_url')) {
                $table->string('image_url')->nullable()->after('description');
            }
        });
    }

    private function ensureToursTable(): void
    {
        if (!Schema::hasTable('tours')) {
            Schema::create('tours', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')
                    ->constrained('categories')
                    ->restrictOnDelete();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description');
                $table->string('location');
                $table->string('image_url')->nullable();
                $table->timestamps();
            });
            return;
        }

        Schema::table('tours', function (Blueprint $table) {
            if (!Schema::hasColumn('tours', 'category_id')) {
                $table->foreignId('category_id')
                    ->after('id')
                    ->constrained('categories')
                    ->restrictOnDelete();
            }
        });
    }
};

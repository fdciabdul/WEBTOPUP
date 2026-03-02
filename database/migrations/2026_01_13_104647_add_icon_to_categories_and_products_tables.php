<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add icon column to categories table if not exists
        if (!Schema::hasColumn('categories', 'icon')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('icon')->nullable()->after('slug');
            });
        }

        // Add icon column to products table if not exists
        if (!Schema::hasColumn('products', 'icon')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('icon')->nullable()->after('slug');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('categories', 'icon')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('icon');
            });
        }

        if (Schema::hasColumn('products', 'icon')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('icon');
            });
        }
    }
};

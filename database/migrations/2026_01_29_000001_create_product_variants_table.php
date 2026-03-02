<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add new columns to products table for parent product concept
        Schema::table('products', function (Blueprint $table) {
            // Only add columns that don't exist
            if (!Schema::hasColumn('products', 'variant_mode')) {
                $table->string('variant_mode')->default('simple')->after('icon'); // simple or nested
            }
            if (!Schema::hasColumn('products', 'input_fields')) {
                $table->json('input_fields')->nullable()->after('meta_data'); // Custom input fields for checkout
            }
            if (!Schema::hasColumn('products', 'notes')) {
                $table->json('notes')->nullable()->after('meta_data'); // Product notes
            }
            if (!Schema::hasColumn('products', 'is_best_seller')) {
                $table->boolean('is_best_seller')->default(false)->after('is_featured');
            }
        });

        // Create product_variants table
        if (!Schema::hasTable('product_variants')) {
            Schema::create('product_variants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('group_name')->nullable(); // For nested mode: "Followers IG", "Likes IG"
                $table->string('name'); // Variant name: "86 Diamonds", "1000 Followers"
                $table->string('provider_code')->nullable();
                $table->decimal('price_visitor', 15, 2)->default(0);
                $table->decimal('price_reseller', 15, 2)->default(0);
                $table->decimal('price_vip', 15, 2)->default(0);
                $table->decimal('price_vvip', 15, 2)->default(0);
                $table->decimal('provider_price', 15, 2)->default(0);
                $table->boolean('is_unlimited_stock')->default(true);
                $table->integer('stock')->default(0);
                $table->string('download_link')->nullable(); // For digital products
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->integer('total_sales')->default(0);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['product_id', 'is_active']);
                $table->index('group_name');
                $table->index('provider_code');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'variant_mode')) {
                $table->dropColumn('variant_mode');
            }
            if (Schema::hasColumn('products', 'input_fields')) {
                $table->dropColumn('input_fields');
            }
            if (Schema::hasColumn('products', 'notes')) {
                $table->dropColumn('notes');
            }
            if (Schema::hasColumn('products', 'is_best_seller')) {
                $table->dropColumn('is_best_seller');
            }
        });
    }
};

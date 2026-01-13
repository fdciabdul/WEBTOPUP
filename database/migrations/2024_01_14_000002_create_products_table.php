<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('provider')->default('digiflazz');
            $table->string('provider_code')->nullable();
            $table->decimal('price_visitor', 15, 2);
            $table->decimal('price_reseller', 15, 2);
            $table->decimal('price_reseller_vip', 15, 2);
            $table->decimal('price_reseller_vvip', 15, 2);
            $table->decimal('provider_price', 15, 2)->default(0);
            $table->boolean('is_unlimited_stock')->default(true);
            $table->integer('stock')->default(0);
            $table->integer('min_order')->default(1);
            $table->integer('max_order')->default(1);
            $table->string('status')->default('active');
            $table->integer('total_sales')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->json('meta_data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'status']);
            $table->index('provider_code');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

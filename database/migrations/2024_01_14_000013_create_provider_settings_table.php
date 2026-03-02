<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider_type'); // topup, payment
            $table->string('provider_name'); // digiflazz, apigames, manual, ipaymu, midtrans
            $table->boolean('is_active')->default(false);
            $table->boolean('is_default')->default(false);
            $table->json('credentials')->nullable(); // Store API keys securely
            $table->json('config')->nullable(); // Additional configuration
            $table->integer('priority')->default(0); // For fallback order
            $table->timestamps();
            
            $table->unique(['provider_type', 'provider_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_settings');
    }
};

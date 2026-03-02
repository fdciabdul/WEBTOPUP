<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auto_delete_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->text('description')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->integer('days')->default(30); // Delete after X days
            $table->timestamp('last_run_at')->nullable();
            $table->integer('last_deleted_count')->default(0);
            $table->timestamps();
        });

        // Seed default auto delete settings
        DB::table('auto_delete_settings')->insert([
            ['key' => 'activity_logs', 'label' => 'Activity Logs', 'description' => 'Hapus log aktivitas yang lebih lama dari X hari', 'is_enabled' => true, 'days' => 90, 'last_run_at' => null, 'last_deleted_count' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'failed_transactions', 'label' => 'Transaksi Gagal', 'description' => 'Hapus transaksi gagal/expired yang lebih lama dari X hari', 'is_enabled' => true, 'days' => 30, 'last_run_at' => null, 'last_deleted_count' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'pending_transactions', 'label' => 'Transaksi Pending', 'description' => 'Hapus transaksi pending yang lebih lama dari X hari', 'is_enabled' => false, 'days' => 7, 'last_run_at' => null, 'last_deleted_count' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'guest_orders', 'label' => 'Guest Orders', 'description' => 'Hapus data order tamu yang lebih lama dari X hari', 'is_enabled' => false, 'days' => 60, 'last_run_at' => null, 'last_deleted_count' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_delete_settings');
    }
};

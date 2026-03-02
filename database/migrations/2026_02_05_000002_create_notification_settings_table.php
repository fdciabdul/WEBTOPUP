<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('channel'); // email, whatsapp, telegram
            $table->string('label');
            $table->text('description')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->json('config')->nullable(); // Additional config like templates
            $table->timestamps();
        });

        // Seed default notification settings
        DB::table('notification_settings')->insert([
            ['key' => 'order_created', 'channel' => 'whatsapp', 'label' => 'Pesanan Baru', 'description' => 'Notifikasi saat ada pesanan baru', 'is_enabled' => true, 'config' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'order_paid', 'channel' => 'whatsapp', 'label' => 'Pembayaran Berhasil', 'description' => 'Notifikasi saat pembayaran dikonfirmasi', 'is_enabled' => true, 'config' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'order_completed', 'channel' => 'whatsapp', 'label' => 'Pesanan Selesai', 'description' => 'Notifikasi saat pesanan selesai diproses', 'is_enabled' => true, 'config' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'order_failed', 'channel' => 'whatsapp', 'label' => 'Pesanan Gagal', 'description' => 'Notifikasi saat pesanan gagal', 'is_enabled' => true, 'config' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'low_balance', 'channel' => 'email', 'label' => 'Saldo Rendah', 'description' => 'Notifikasi saat saldo provider rendah', 'is_enabled' => true, 'config' => json_encode(['threshold' => 100000]), 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'new_member', 'channel' => 'email', 'label' => 'Member Baru', 'description' => 'Notifikasi saat ada member baru mendaftar', 'is_enabled' => false, 'config' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};

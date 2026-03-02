<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer, json
            $table->string('label');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed default security settings
        DB::table('security_settings')->insert([
            ['key' => 'login_max_attempts', 'value' => '5', 'type' => 'integer', 'label' => 'Max Login Attempts', 'description' => 'Jumlah maksimal percobaan login sebelum diblokir', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'login_lockout_duration', 'value' => '15', 'type' => 'integer', 'label' => 'Lockout Duration (menit)', 'description' => 'Durasi blokir setelah melebihi batas login', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'session_lifetime', 'value' => '120', 'type' => 'integer', 'label' => 'Session Lifetime (menit)', 'description' => 'Durasi session aktif', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'force_https', 'value' => '1', 'type' => 'boolean', 'label' => 'Force HTTPS', 'description' => 'Paksa semua request menggunakan HTTPS', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'ip_whitelist', 'value' => '', 'type' => 'string', 'label' => 'IP Whitelist', 'description' => 'Daftar IP yang diizinkan akses admin (pisahkan dengan koma)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'label' => 'Maintenance Mode', 'description' => 'Aktifkan mode maintenance', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'recaptcha_enabled', 'value' => '0', 'type' => 'boolean', 'label' => 'Enable reCAPTCHA', 'description' => 'Aktifkan reCAPTCHA pada form login', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('security_settings');
    }
};

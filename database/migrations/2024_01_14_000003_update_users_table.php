<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('role')->default('member')->after('password');
            $table->string('level')->default('visitor')->after('role');
            $table->decimal('balance', 15, 2)->default(0)->after('level');
            $table->integer('total_transactions')->default(0)->after('balance');
            $table->decimal('total_spending', 15, 2)->default(0)->after('total_transactions');
            $table->boolean('is_active')->default(true)->after('total_spending');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            $table->softDeletes();

            $table->index('role');
            $table->index('level');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'role', 'level', 'balance', 
                'total_transactions', 'total_spending', 
                'is_active', 'last_login_at', 'last_login_ip'
            ]);
            $table->dropSoftDeletes();
        });
    }
};

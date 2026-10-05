<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('password')->nullable()->after('email');
            $table->rememberToken()->after('password');
            $table->string('password_reset_token', 100)->nullable()->after('remember_token');
            $table->timestamp('password_reset_sent_at')->nullable()->after('password_reset_token');
            $table->timestamp('last_login_at')->nullable()->after('password_reset_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'password',
                'remember_token',
                'password_reset_token',
                'password_reset_sent_at',
                'last_login_at',
            ]);
        });
    }
};

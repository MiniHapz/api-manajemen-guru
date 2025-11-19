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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Identitas pengguna
            $table->string('name');
            $table->string('username')->unique(); // Login pakai username
            $table->string('password');

            // Role & relasi
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->foreignId('sekolah_id')->nullable()
                  ->constrained('sekolah')
                  ->onDelete('set null');

            // Status & aktivitas
            $table->enum('status', ['Aktif', 'NonAktif'])->default('Aktif');
            $table->timestamp('last_login_at')->nullable();

            // Token & waktu
            $table->rememberToken();
            $table->timestamps();
        });

        // Password reset pakai username (bukan email)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('username')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Sessions table
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};

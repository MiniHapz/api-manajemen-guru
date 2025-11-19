<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ===== Tabel guru =====
        Schema::create('guru', function (Blueprint $table) {
            $table->string('nip', 18)->primary(); // NIP 18 digit, primary key
            $table->string('nama_lengkap', 150);
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->enum('status_kepegawaian', ['pns', 'p3k', 'p3k_paruh_waktu'])->nullable();
            $table->string('pendidikan_terakhir', 100)->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email', 100)->nullable()->unique();
            $table->text('alamat')->nullable();
            $table->date('tanggal_bergabung')->nullable();
            $table->date('tanggal_pensiun')->nullable();
            $table->foreignId('sekolah_id')->constrained('sekolah')->onDelete('cascade');
            
            // Tambahan kolom jam mengajar per minggu
            $table->unsignedInteger('jam_mengajar_per_minggu')->nullable()->default(0);

            $table->timestamps();
        });

    }
    public function down(): void
    {
        Schema::dropIfExists('guru_mapel');
        Schema::dropIfExists('guru');
    }
};

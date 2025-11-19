<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->string('kepala_sekolah_nip', 18)->nullable()->after('npsn');

            // tambahkan foreign key manual ke kolom non-id
            $table->foreign('kepala_sekolah_nip')
                  ->references('nip')
                  ->on('guru')
                  ->nullOnDelete();
        });
    }

    public function down(): void {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->dropForeign(['kepala_sekolah_nip']);
            $table->dropColumn('kepala_sekolah_nip');
        });
    }
};

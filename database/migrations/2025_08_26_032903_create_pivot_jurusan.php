<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('guru_mapel', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke guru
            $table->string('guru_nip', 18)->nullable();
            $table->foreign('guru_nip')
                ->references('nip')
                ->on('guru')
                ->onDelete('cascade');

            // Relasi ke mapel
            $table->foreignId('mapel_id')
                ->constrained('mapel')
                ->onDelete('cascade');

            // Relasi ke sekolah
            $table->foreignId('sekolah_id')
                ->nullable()
                ->constrained('sekolah')
                ->onDelete('cascade');

            // Penanda apakah mapel ini hasil custom sekolah
            $table->boolean('custom')->default(true);

            $table->timestamps();

            // Hindari duplikasi entri
            $table->unique(
                ['guru_nip', 'mapel_id', 'sekolah_id'], 
                'unik_guru_mapel'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_mapel');
    }
};

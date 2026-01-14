<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    use HasFactory;

    protected $table = 'sekolah';

    protected $fillable = [
        'nama_sekolah',
        'jenjang',
        'akreditasi',
        'npsn',
        'alamat',
        'kepala_sekolah_nip',
        'status',
    ];

    // ===== RELASI =====

    // Relasi: 1 sekolah punya banyak guru
    public function guru()
    {
        return $this->hasMany(Guru::class, 'sekolah_id');
    }

    // Relasi: 1 sekolah bisa punya banyak user operator
    public function users()
    {
        return $this->hasMany(User::class, 'sekolah_id');
    }

    // Relasi: Kepala sekolah (1 guru bisa jadi kepala di 1 sekolah)
public function kepalaSekolah()
{
    return $this->belongsTo(Guru::class, 'kepala_sekolah_nip', 'nip');
}


    // ===== ACCESSOR TAMBAHAN =====
    public function getNamaLengkapKepalaAttribute()
    {
        return $this->kepalaSekolah ? $this->kepalaSekolah->nama_lengkap : '-';
    }

    public function getLabelAttribute()
    {
        return "{$this->nama_sekolah} ({$this->jenjang})";
    }

    // ===== RELASI MAPEL SESUAI JURUSAN =====
    public function mapel()
    {
        return $this->hasManyThrough(
            Mapel::class,// pivot baru
            'sekolah_id',        // FK di mapel_jurusan ke sekolah
            'id',                // FK di mapel ke mapel_jurusan
            'id',                // PK sekolah
            'mapel_id'           // FK mapel_jurusan ke mapel
        );
    }

    public function operator()
{
    return $this->hasOne(\App\Models\User::class, 'sekolah_id')->where('role', 'user');
}

}

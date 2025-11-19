<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    use HasFactory;

    protected $table = 'mapel';
    protected $fillable = ['nama_mapel'];

    public function guruMapel()
{
    return $this->hasMany(GuruMapel::class, 'mapel_id');
}


    // ===== Relasi ke guru melalui pivot mapel ↔ guru ↔ jurusan/sekolah =====
    public function guru()
{
    return $this->belongsToMany(Guru::class, 'guru_mapel', 'mapel_id', 'guru_nip')
                ->withPivot('sekolah_id')
                ->withTimestamps();
}

}

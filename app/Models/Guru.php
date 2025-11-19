<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'guru';

    // ===== Primary key khusus NIP =====
    protected $primaryKey = 'nip';
    public $incrementing = false; // karena NIP string
    protected $keyType = 'string';

    
    protected $with = ['mapel', 'sekolah']; // 🟢 Tambah ini
    protected $appends = ['nama_mapel'];    // 🟢 Tambah ini juga
    
    protected $fillable = [
        'nip',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'status_kepegawaian',
        'jabatan',
        'pendidikan_terakhir',
        'telepon',
        'email',
        'alamat',
        'tanggal_bergabung',
        'tanggal_pensiun',
        'sekolah_id',
        'jam_mengajar_per_minggu', 
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_bergabung' => 'date',
        'tanggal_pensiun' => 'date',
    ];

    // ======== RELASI ========

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    public function sekolahDikepalai()
{
    return $this->hasOne(Sekolah::class, 'kepala_sekolah_nip', 'nip');
}

    // Relasi ke mapel melalui pivot mapel ↔ jurusan ↔ sekolah
public function mapel()
{
    return $this->belongsToMany(Mapel::class, 'guru_mapel', 'guru_nip', 'mapel_id')
                ->withPivot('sekolah_id')
                ->withTimestamps();
}


    // public function dokumen()
    // {
    //     return $this->hasMany(Dokumen::class, 'nip');
    // }

    // ======== ACCESSOR / HELPER ========

    public function getUmurAttribute()
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->age : null;
    }

    public function getSudahPensiunAttribute()
    {
        return $this->tanggal_pensiun
            ? now()->greaterThanOrEqualTo($this->tanggal_pensiun)
            : false;
    }

    

    // Ambil list nama mapel sebagai array
    public function getNamaMapelAttribute()
    {
        return $this->mapel->pluck('nama_mapel')->toArray();
    }

    public function getRouteKeyName()
    {
        return 'nip';
    }
}

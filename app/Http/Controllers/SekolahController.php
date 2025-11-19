<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SekolahController extends Controller
{
    // Ambil semua data sekolah
    public function index()
    {
        $sekolah = Sekolah::with('kepalaSekolah')
                    ->doesntHave('operator') // hanya sekolah tanpa operator
                    ->get();

        return response()->json([
            'status' => 'success',
            'data' => $sekolah
        ]);
    }

    // Tambah sekolah baru (tanpa kepala sekolah)
    public function store(Request $request)
    {
        $request->validate([
            'namaSekolah' => 'required|string|max:150',
            'jenjang' => 'required|in:SMA,SMK,SLB',
            'akreditasi' => 'nullable|string|max:2',
            'npsn' => 'required|string|size:8|unique:sekolah,npsn',
            'alamatSekolah' => 'nullable|string',
        ]);

        // 💾 Simpan data sekolah tanpa kepala sekolah dulu
        $sekolah = Sekolah::create([
            'nama_sekolah' => $request->namaSekolah,
            'jenjang' => $request->jenjang,
            'akreditasi' => $request->akreditasi,
            'npsn' => $request->npsn,
            'alamat' => $request->alamatSekolah,
            'kepala_sekolah_nip' => null, // default kosong
        ]);
        // ================================
// 🧩 Auto Insert Mapel Default
// ================================

// Mapel default diambil dari tabel mapel global
$defaultMapels = [
    'Pendidikan Pancasila & Kewarganegaraan (PPKn)',
    'Bahasa Indonesia',
    'Bahasa Inggris',
    'Matematika',
    'Seni Budaya',
    'Pendidikan Jasmani, Olahraga, dan Kesehatan (PJOK)',
];

// Ambil mapel global berdasarkan nama
$mapels = DB::table('mapel')
    ->whereIn('nama_mapel', $defaultMapels)
    ->pluck('id');

$insert = [];
foreach ($mapels as $mapelId) {
    $insert[] = [
        'guru_nip'   => null,
        'mapel_id'   => $mapelId,
        'sekolah_id' => $sekolah->id,
        'custom'     => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ];
}

DB::table('guru_mapel')->insert($insert);


        return response()->json([
            'status' => 'success',
            'message' => 'Data sekolah berhasil disimpan (kepala sekolah dapat diisi otomatis nanti).',
            'data' => $sekolah->load('kepalaSekolah')
        ], 201);
    }

    // Detail sekolah
public function show($id)
{
    $sekolah = Sekolah::with('kepalaSekolah')->find($id);

    if (!$sekolah) {
        return response()->json([
            'message' => 'Data sekolah tidak ditemukan',
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => $sekolah
    ]);
}

    // Update dinonaktifkan (karena sekolah tidak bisa diubah)
    public function update(Request $request, $id)
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Data sekolah tidak dapat diubah setelah dibuat.'
        ], 403);
    }

    // Hapus sekolah
    public function destroy($id)
    {
        Sekolah::destroy($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Data sekolah berhasil dihapus'
        ]);
    }
}

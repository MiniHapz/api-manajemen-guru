<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapelController extends Controller
{
    // ===== TAMPILKAN SEMUA MAPEL UNTUK SEKOLAH OPERATOR =====
    public function index(Request $request)
    {
        $operator = $request->user();
        $sekolahId = $operator->sekolah_id ?? null;

        if (!$sekolahId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Operator belum terhubung ke sekolah manapun.'
            ], 403);
        }

        // Ambil data mapel dengan jumlah guru di sekolah ini
        $mapels = DB::table('guru_mapel')
            ->join('mapel', 'guru_mapel.mapel_id', '=', 'mapel.id')
            ->where('guru_mapel.sekolah_id', $sekolahId)
            ->select(
                'mapel.id',
                'mapel.nama_mapel',
                DB::raw('COUNT(DISTINCT guru_mapel.guru_nip) as jumlahGuru')
            )
            ->groupBy('mapel.id', 'mapel.nama_mapel')
            ->orderBy('mapel.nama_mapel')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $mapels
        ]);
    }

    // ===== TAMBAH MAPEL =====
public function store(Request $request)
{
    $operator = $request->user();
    $sekolahId = $operator->sekolah_id;

    if (!$sekolahId) {
        return response()->json([
            'status' => 'error',
            'message' => 'Operator belum terhubung ke sekolah manapun.'
        ], 403);
    }

    $request->validate([
        'nama_mapel' => 'required|string|max:100',
    ]);

    // Pastikan mapel ada secara global
    $mapel = Mapel::firstOrCreate(['nama_mapel' => $request->nama_mapel]);

    // Gunakan updateOrInsert supaya aman dari duplikat saat request bersamaan
    DB::table('guru_mapel')->updateOrInsert(
        [
            'mapel_id' => $mapel->id,
            'sekolah_id' => $sekolahId,
            'guru_nip' => null,
        ],
        [
            'custom' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    );

    return response()->json([
        'status' => 'success',
        'message' => 'Mapel berhasil ditambahkan untuk sekolah Anda',
        'data' => $mapel
    ], 201);
}

    // ===== UPDATE MAPEL =====
    public function update(Request $request, $id)
    {
        $mapel = Mapel::findOrFail($id);

        $request->validate([
            'nama_mapel' => 'required|string|max:100|unique:mapel,nama_mapel,' . $id,
        ]);

        $mapel->update($request->only('nama_mapel'));

        return response()->json([
            'status' => 'success',
            'message' => 'Mapel berhasil diperbarui',
            'data' => $mapel
        ]);
    }

    // ===== HAPUS MAPEL =====
    public function destroy(Request $request, $id)
    {
        $operator = $request->user();
        $sekolahId = $operator->sekolah_id;

        // Hapus relasi mapel untuk sekolah operator ini saja
        $deleted = DB::table('guru_mapel')
            ->where('mapel_id', $id)
            ->where('sekolah_id', $sekolahId)
            ->delete();

        if ($deleted) {
            return response()->json([
                'status' => 'success',
                'message' => 'Mapel berhasil dihapus untuk sekolah Anda'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Mapel tidak ditemukan untuk sekolah Anda'
            ], 404);
        }
    }
}

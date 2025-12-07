<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruController extends Controller
{
    // ===== Tampilkan semua guru =====
    public function index(Request $request)
    {
        $user = $request->user();

        $guru = Guru::with(['sekolah', 'mapel'])
            ->when(!in_array($user->role, ['admin', 'admin_cabdin']), function ($q) use ($user) {
                $q->where('sekolah_id', $user->sekolah_id);
            })
            ->get();

        return response()->json([
            'status' => 'success',
            'count' => $guru->count(),
            'data' => $guru,
        ]);
    }

    // ===== Simpan guru baru =====
public function store(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->sekolah_id) {
            return response()->json(['status' => 'error', 'message' => 'User tidak memiliki sekolah.'], 403);
        }

        $validated = $request->validate([
            
            'nip' => 'required|digits:18|unique:guru,nip',
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'status_kepegawaian' => 'nullable|string|max:50',
            'jabatan' => 'nullable|string|max:100',
            'pendidikan_terakhir' => 'nullable|string|max:100',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:guru,email',
            'alamat' => 'nullable|string',
            'tanggal_bergabung' => 'nullable|date',
            'jam_mengajar_per_minggu' => 'nullable|integer|min:0|max:40',
            'mapel_id' => 'nullable|array',
            'mapel_id.*' => 'exists:mapel,id',
        ]);
// Cek apakah user memasukkan jabatan Kepala Sekolah
if (isset($validated['jabatan']) && strtolower($validated['jabatan']) === 'kepala sekolah') {

    // Cek apakah sekolah sudah punya kepala sekolah
    $kepsekSudahAda = Guru::where('sekolah_id', $user->sekolah_id)
        ->whereRaw('LOWER(jabatan) = "kepala sekolah"')
        ->exists();

    if ($kepsekSudahAda) {
        return response()->json([
            'status' => 'error',
            'message' => 'Sekolah ini sudah memiliki Kepala Sekolah.'
        ], 400);
    }
}

        if (!empty($validated['tanggal_lahir'])) {
            $lahir = new \DateTime($validated['tanggal_lahir']);
            $lahir->modify('+60 years +1 month');
            $validated['tanggal_pensiun'] = $lahir->format('Y-m-d');
        }

        $validated['sekolah_id'] = $user->sekolah_id;

        $guru = Guru::create($validated);

        // ===== Simpan ke pivot guru_mapel =====
        if ($request->has('mapel_id')) {
            foreach ($request->mapel_id as $mapelId) {
                DB::table('guru_mapel')->updateOrInsert([
                    'guru_nip' => $guru->nip,
                    'mapel_id' => $mapelId,
                    'sekolah_id' => $user->sekolah_id,
                ], [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 🟢 Jika jabatan Kepala Sekolah, update kolom di tabel sekolah
        if (strtolower($guru->jabatan) === 'kepala sekolah') {
            Sekolah::where('id', $guru->sekolah_id)
                ->update(['kepala_sekolah_nip' => $guru->nip]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Guru berhasil ditambahkan',
            'data' => $guru
        ], 201);
    }

    // ===== Detail guru =====
    public function show(Request $request, $nip)
    {
        $user = $request->user();
        
        $guru = Guru::with(['sekolah', 'mapel'])
    ->when(!in_array($user->role, ['admin', 'admin_cabdin']), function ($q) use ($user) {
    $q->where('sekolah_id', $user->sekolah_id);
})

    ->where('nip', $nip)
    ->first();

        if (!$guru) {
            return response()->json(['message' => 'Guru tidak ditemukan'], 404);
        }

        return response()->json($guru);
    }

    // ===== Update guru =====
 // ===== Update guru =====
    public function update(Request $request, $nip)
    {
        $user = $request->user();

        $guru = Guru::where('nip', $nip)
            ->when($user->role !== 'admin_cabdin', fn($q) => $q->where('sekolah_id', $user->sekolah_id))
            ->firstOrFail();

        $validated = $request->validate([
            'nip' => 'nullable|string|size:18',
            'nama_lengkap' => 'required|string|max:150',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'status_kepegawaian' => 'nullable|string|max:50',
            'pendidikan_terakhir' => 'nullable|string|max:100',
            'jabatan' => 'nullable|string|max:100',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:guru,email,' . $nip . ',nip',
            'alamat' => 'nullable|string',
            'tanggal_bergabung' => 'nullable|date',
            'jam_mengajar_per_minggu' => 'nullable|integer|min:0|max:40',
            'mapel_id' => 'nullable|array',
            'mapel_id.*' => 'exists:mapel,id',
        ]);
        unset($validated['nip']);


        // Cek apakah user memasukkan jabatan Kepala Sekolah
if (isset($validated['jabatan']) && strtolower($validated['jabatan']) === 'kepala sekolah') {

    // Cek apakah sekolah sudah punya kepala sekolah
    $kepsekSudahAda = Guru::where('sekolah_id', $user->sekolah_id)
        ->whereRaw('LOWER(jabatan) = "kepala sekolah"')
        ->where('nip', '!=', $nip) 
        ->exists();

    if ($kepsekSudahAda) {
        return response()->json([
            'status' => 'error',
            'message' => 'Sekolah ini sudah memiliki Kepala Sekolah.'
        ], 400);
    }
}

        if (!empty($validated['tanggal_lahir'])) {
            $lahir = new \DateTime($validated['tanggal_lahir']);
            $lahir->modify('+60 years +1 month');
            $validated['tanggal_pensiun'] = $lahir->format('Y-m-d');
        }

        $validated['sekolah_id'] = $user->sekolah_id;
        $guru->update($validated);

        // ===== Update pivot guru_mapel =====
        if ($request->has('mapel_id')) {
            DB::table('guru_mapel')->where('guru_nip', $guru->nip)->delete();

            foreach ($request->mapel_id as $mapelId) {
                DB::table('guru_mapel')->insert([
                    'guru_nip' => $guru->nip,
                    'mapel_id' => $mapelId,
                    'sekolah_id' => $user->sekolah_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 🟢 Update otomatis kolom kepala sekolah di tabel sekolah
        $sekolah = Sekolah::find($guru->sekolah_id);
        if ($sekolah) {
            if (strtolower($guru->jabatan) === 'kepala sekolah') {
                $sekolah->update(['kepala_sekolah_nip' => $guru->nip]);
            } elseif ($sekolah->kepala_sekolah_nip === $guru->nip) {
                // Jika guru ini dulu kepala sekolah tapi jabatannya berubah
                $sekolah->update(['kepala_sekolah_nip' => null]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Guru berhasil diperbarui',
            'data' => $guru
        ]);
    }
    // ===== Guru akan pensiun =====
    public function akanPensiun(Request $request)
    {
        $user = $request->user();
        $batas = now()->addYears(3);

        $guru = Guru::with(['sekolah', 'mapel'])
            ->whereDate('tanggal_pensiun', '<=', $batas)
            ->when($user->role !== 'admin_cabdin', fn($q) => $q->where('sekolah_id', $user->sekolah_id))
            ->orderBy('tanggal_pensiun', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'count' => $guru->count(),
            'data' => $guru
        ]);
    }

    // ===== Statistik Guru =====
    public function summary(Request $request)
    {
        $user = $request->user();

        $query = Guru::query();
        if ($user->role !== 'admin_cabdin') {
            $query->where('sekolah_id', $user->sekolah_id);
        }

        $totalGuru = $query->count();
        $totalLaki = Guru::where('jenis_kelamin', 'L')
            ->when($user->role !== 'admin_cabdin', fn($q) => $q->where('sekolah_id', $user->sekolah_id))
            ->count();
        $totalPerempuan = Guru::where('jenis_kelamin', 'P')
            ->when($user->role !== 'admin_cabdin', fn($q) => $q->where('sekolah_id', $user->sekolah_id))
            ->count();
        $pensiunTahunIni = Guru::whereYear('tanggal_pensiun', now()->year)
            ->when($user->role !== 'admin_cabdin', fn($q) => $q->where('sekolah_id', $user->sekolah_id))
            ->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total' => $totalGuru,
                'laki_laki' => $totalLaki,
                'perempuan' => $totalPerempuan,
                'pensiun_tahun_ini' => $pensiunTahunIni
            ]
        ]);
    }

    // ===== Hapus guru =====
    public function destroy(Request $request, $nip)
    {
        $user = $request->user();

        $guru = Guru::where('nip', $nip)
            ->when($user->role !== 'admin_cabdin', fn($q) => $q->where('sekolah_id', $user->sekolah_id))
            ->firstOrFail();

        // hapus pivot-nya juga biar rapi
        DB::table('guru_mapel')->where('guru_nip', $guru->nip)->delete();
        $guru->delete();

        return response()->json(['message'=>'Guru berhasil dihapus']);
    }
}

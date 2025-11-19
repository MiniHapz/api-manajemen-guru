<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardOperatorController extends Controller
{
    /**
     * Ambil data ringkasan dan daftar guru untuk dashboard operator.
     */
    public function index(Request $request)
    {
        // ====== Ambil sekolah_id dari operator ======
        $sekolahId = $request->user()->sekolah_id ?? $request->sekolah_id;
        if (!$sekolahId) {
            return response()->json([
                'message' => 'sekolah_id tidak ditemukan pada akun operator',
            ], 400);
        }

        // ====== Ambil guru sesuai sekolah ======
        $guruList = Guru::with(['mapel', 'sekolah'])
            ->where('sekolah_id', $sekolahId)
            ->get();

        // ====== Ambil total mapel unik di sekolah ======
        $mapelList = DB::table('guru_mapel')
            ->join('mapel', 'guru_mapel.mapel_id', '=', 'mapel.id')
            ->where('guru_mapel.sekolah_id', $sekolahId)
            ->distinct('mapel_id')
            ->pluck('mapel.nama_mapel');

        $totalMapel = $mapelList->count();

        // ====== Breakdown guru per status ======
        $totalGuruByStatus = $guruList->groupBy('status_kepegawaian')
            ->map(fn($group) => $group->count())
            ->toArray();

        // ====== Guru yang akan pensiun 3 tahun ke depan ======
        $limitPensiun = Carbon::now()->addYears(3);
        $akanPensiunByStatus = $guruList->filter(fn($g) =>
            $g->tanggal_pensiun && $g->tanggal_pensiun->between(Carbon::now(), $limitPensiun)
        )->groupBy('status_kepegawaian')
         ->map(fn($group) => $group->count())
         ->toArray();

        // ====== Ubah meta menjadi string supaya React bisa render ======
        $totalGuruMeta = collect($totalGuruByStatus)
            ->map(fn($count, $status) => "{$count} {$status}")
            ->implode(', ');

        $akanPensiunMeta = collect($akanPensiunByStatus)
            ->map(fn($count, $status) => "{$count} {$status}")
            ->implode(', ');

        // ====== Format guru detail ======
        $guruListFormatted = $guruList->map(function ($guru) {
            return [
                'id' => $guru->nip,
                'nip' => $guru->nip,
                'nama' => $guru->nama_lengkap,
                'mapel' => $guru->mapel->pluck('nama_mapel')->toArray(),
                'jam_mengajar_per_minggu' => $guru->jam_mengajar_per_minggu ?? 0,
                'status' => $guru->status_kepegawaian,
                'alamat' => $guru->alamat,
                'jenis_kelamin' => $guru->jenis_kelamin,
                'tanggal_pensiun' => optional($guru->tanggal_pensiun)->format('Y-m-d'),
                'tanggal_bergabung' => optional($guru->tanggal_bergabung)->format('Y-m-d'),
                'umur' => $guru->umur,
                'sekolah' => $guru->sekolah->nama_sekolah ?? '-',
            ];
        });

        // ====== List guru akan pensiun 3 tahun ======
        $pensiunList = $guruList->filter(fn($g) =>
            $g->tanggal_pensiun && $g->tanggal_pensiun->between(Carbon::now(), $limitPensiun)
        )->map(function($g){
            return [
                'id' => $g->nip,
                'nip' => $g->nip,
                'nama' => $g->nama_lengkap,
                'jab' => $g->status_kepegawaian,
                'tgl' => optional($g->tanggal_pensiun)->format('Y-m-d'),
                'status' => $g->status_kepegawaian,
                'sekolah' => $g->sekolah->nama_sekolah ?? '-',
            ];
        });

        // ====== Response JSON ======
        return response()->json([
            'summary' => [
                'totalGuru' => [
                    'label' => 'Total Guru',
                    'value' => $guruList->count() . ' Guru',
                    'meta' => $totalGuruMeta,
                ],
                'mapel' => [
                    'label' => 'Total Mapel',
                    'value' => "{$totalMapel} Mapel",
                    'meta' => implode(', ', $mapelList->toArray()),
                ],
                'akanPensiun' => [
                    'label' => 'Guru Akan Pensiun',
                    'value' => $pensiunList->count() . ' Guru',
                    'meta' => $akanPensiunMeta,
                ],
            ],
            'guru' => $guruListFormatted,
            'pensiun_3_tahun' => $pensiunList,
        ]);
    }
}

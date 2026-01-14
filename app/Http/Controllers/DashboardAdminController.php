<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\Guru;
use Illuminate\Http\Request;

class DashboardAdminController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | 🔹 STATISTIK UTAMA (HANYA SEKOLAH AKTIF)
        |--------------------------------------------------------------------------
        */
        $totalSekolah = Sekolah::count();
        $totalGuru = Guru::count();

        // Guru yang akan pensiun (>= 57 tahun)
        $guruAkanPensiun = Guru::whereRaw(
            'TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 57'
        )->count();

        /*
        |--------------------------------------------------------------------------
        | 🔹 STATISTIK JENIS GURU
        |--------------------------------------------------------------------------
        */
        $jumlahPNS = Guru::where('status_kepegawaian', 'PNS')->count();
        $jumlahP3K = Guru::where('status_kepegawaian', 'P3K')->count();
        $jumlahParuhWaktu = Guru::where('status_kepegawaian', 'p3k_paruh_waktu')->count();

        /*
        |--------------------------------------------------------------------------
        | 🔹 STATISTIK JENJANG SEKOLAH (AKTIF SAJA)
        |--------------------------------------------------------------------------
        */
        $jumlahSMK = Sekolah::where('status', 'aktif')
            ->where('jenjang', 'SMK')
            ->count();

        $jumlahSMA = Sekolah::where('status', 'aktif')
            ->where('jenjang', 'SMA')
            ->count();

        $jumlahSLB = Sekolah::where('status', 'aktif')
            ->where('jenjang', 'SLB')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | 🔹 DAFTAR SEKOLAH (AKTIF, BATAS 10)
        |--------------------------------------------------------------------------
        */
        $daftarSekolah = Sekolah::withCount('guru')
            ->get()
            ->map(function ($s) {
                $pns = $s->guru()
                    ->where('status_kepegawaian', 'PNS')
                    ->count();

                $p3k = $s->guru()
                    ->where('status_kepegawaian', 'P3K')
                    ->count();

                $paruh = $s->guru()
                    ->where('status_kepegawaian', 'p3k_paruh_waktu')
                    ->count();

                $pensiun = $s->guru()
                    ->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 57')
                    ->count();

                return [
                    'id'           => $s->id,
                    'nama'         => $s->nama_sekolah,
                    'alamat'       => $s->alamat,
                    'jenjang'      => $s->jenjang,
                     'status' => $s->status,
                    'jumlahGuru'   => $s->guru_count,
                    'pns'          => $pns,
                    'p3k'          => $p3k,
                    'p3k_paruh_waktu'   => $paruh,
                    'masaPensiun'  => $pensiun,
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | 🔹 PIE CHART GURU
        |--------------------------------------------------------------------------
        */
        $totalGuruSemua = max(1, $jumlahPNS + $jumlahP3K + $jumlahParuhWaktu);

        $guruPie = [
    ['label' => 'PNS', 'value' => round(($jumlahPNS / $totalGuruSemua) * 100, 1), 'color' => '#3b82f6'],
    ['label' => 'P3K', 'value' => round(($jumlahP3K / $totalGuruSemua) * 100, 1), 'color' => '#10b981'],
    ['label' => 'p3k_paruh_waktu', 'value' => round(($jumlahParuhWaktu / $totalGuruSemua) * 100, 1), 'color' => '#f59e0b'],
];


        /*
        |--------------------------------------------------------------------------
        | 🔹 PIE CHART SEKOLAH
        |--------------------------------------------------------------------------
        */
        $totalSekolahSemua = max(1, $jumlahSMK + $jumlahSMA + $jumlahSLB);

$sekolahPie = [
    [
        'label' => 'SMK',
        'value' => round(($jumlahSMK / $totalSekolahSemua) * 100, 1),
        'color' => '#3b82f6', // biru
    ],
    [
        'label' => 'SMA',
        'value' => round(($jumlahSMA / $totalSekolahSemua) * 100, 1),
        'color' => '#10b981', // hijau
    ],
    [
        'label' => 'SLB',
        'value' => round(($jumlahSLB / $totalSekolahSemua) * 100, 1),
        'color' => '#f59e0b', // kuning/oranye
    ],
];


        /*
        |--------------------------------------------------------------------------
        | 🔹 RESPONSE
        |--------------------------------------------------------------------------
        */
        return response()->json([
            'stats' => [
                'total_sekolah' => $totalSekolah,
                'total_guru' => $totalGuru,
                'guru_akan_pensiun' => $guruAkanPensiun,
                'detail' => [
                    'pns' => $jumlahPNS,
                    'p3k' => $jumlahP3K,
                    'paruh_waktu' => $jumlahParuhWaktu,
                ],
            ],
            'guru_pie' => $guruPie,
            'sekolah_pie' => $sekolahPie,
            'daftar_sekolah' => $daftarSekolah,
        ]);
    }
}

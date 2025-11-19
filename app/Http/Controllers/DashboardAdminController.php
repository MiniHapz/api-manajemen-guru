<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{
    public function index()
    {
        // 🔹 Statistik Utama
        $totalSekolah = Sekolah::count();
        $totalGuru = Guru::count();

        // Guru yang akan pensiun (misal: umur >= 57 tahun)
        $guruAkanPensiun = Guru::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 57')->count();

        // 🔹 Statistik Jenis Guru
        $jumlahPNS = Guru::where('status_kepegawaian', 'PNS')->count();
        $jumlahP3K = Guru::where('status_kepegawaian', 'P3K')->count();
        $jumlahParuhWaktu = Guru::where('status_kepegawaian', 'P3K Paruh Waktu')->count();

        // 🔹 Statistik Jenjang Sekolah
        $jumlahSMK = Sekolah::where('jenjang', 'SMK')->count();
        $jumlahSMA = Sekolah::where('jenjang', 'SMA')->count();
        $jumlahSLB = Sekolah::where('jenjang', 'SLB')->count();

        // 🔹 Detail daftar sekolah (batas 10 biar ringan)
        $daftarSekolah = Sekolah::withCount('guru')
            ->take(10)
            ->get()
            ->map(function ($s) {
                $pns = $s->guru()->where('status_kepegawaian', 'PNS')->count();
                $p3k = $s->guru()->where('status_kepegawaian', 'P3K')->count();
                $paruh = $s->guru()->where('status_kepegawaian', 'P3K Paruh Waktu')->count();
                $pensiun = $s->guru()
                    ->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 57')
                    ->count();

                return [
                    'id' => $s->id,
                    'nama' => $s->nama_sekolah,
                    'alamat' => $s->alamat,
                    'jenjang' => $s->jenjang,
                    'jumlahGuru' => $s->guru_count,
                    'pns' => $pns,
                    'p3k' => $p3k,
                    'paruhWaktu' => $paruh,
                    'masaPensiun' => $pensiun,
                ];
            });

        // 🔹 Hitung persentase untuk pie chart guru
        $totalGuruSemua = max(1, $jumlahPNS + $jumlahP3K + $jumlahParuhWaktu); // avoid /0
        $guruPie = [
            ['label' => 'PNS', 'value' => round(($jumlahPNS / $totalGuruSemua) * 100, 1), 'color' => '#10b981'],
            ['label' => 'P3K', 'value' => round(($jumlahP3K / $totalGuruSemua) * 100, 1), 'color' => '#f59e0b'],
            ['label' => 'P3K Paruh Waktu', 'value' => round(($jumlahParuhWaktu / $totalGuruSemua) * 100, 1), 'color' => '#ef4444'],
        ];

        // 🔹 Hitung persentase untuk pie chart sekolah
        $totalSekolahSemua = max(1, $jumlahSMK + $jumlahSMA + $jumlahSLB);
        $sekolahPie = [
            ['label' => 'SMK', 'value' => round(($jumlahSMK / $totalSekolahSemua) * 100, 1), 'color' => '#06b6d4'],
            ['label' => 'SMA', 'value' => round(($jumlahSMA / $totalSekolahSemua) * 100, 1), 'color' => '#3b82f6'],
            ['label' => 'SLB', 'value' => round(($jumlahSLB / $totalSekolahSemua) * 100, 1), 'color' => '#8b5cf6'],
        ];

        // 🔹 Format respons JSON
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

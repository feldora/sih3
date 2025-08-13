<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PosPantau;
use App\Models\WilayahSungai;
use App\Models\DataCurahHujan;
use App\Models\DataTinggiMukaAir;
use App\Models\DataKlimatologi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Overview Cards Data
        $overviewData = $this->getOverviewData();
        
        // Data Summary
        $dataSummary = $this->getDataSummary();
        
        // Latest Pos Pantau
        $latestPosPantau = $this->getLatestPosPantau();
        
        // Quick Alerts
        $alerts = $this->getQuickAlerts();
        
        // Pos Pantau by Kabupaten (for simple map data)
        $posByKabupaten = $this->getPosByKabupaten();

        return view('admin.dashboard', compact(
            'overviewData',
            'dataSummary', 
            'latestPosPantau',
            'alerts',
            'posByKabupaten'
        ));
    }

    private function getOverviewData()
    {
        return [
            'total_pos' => PosPantau::count() ?? 0,
            'total_ws' => WilayahSungai::count() ?? 0,
            'total_data' => (DataCurahHujan::count() + DataTinggiMukaAir::count() + DataKlimatologi::count()) ?? 0,
            'last_update' => PosPantau::latest('updated_at')->value('updated_at') ?? 0,
        ];
    }

    private function getDataSummary()
    {
        // Status Pos Pantau
        $posStatus = PosPantau::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Jenis Pos Pantau  
        $jenisPos = PosPantau::select('jenis_pos', DB::raw('count(*) as total'))
            ->groupBy('jenis_pos')
            ->pluck('total', 'jenis_pos')
            ->toArray();

        // Data Records Count
        $dataRecords = [
            'curah_hujan' => DataCurahHujan::count(),
            'tinggi_muka_air' => DataTinggiMukaAir::count(),
            'klimatologi' => DataKlimatologi::count()
        ];

        return [
            'pos_status' => $posStatus,
            'jenis_pos' => $jenisPos,
            'data_records' => $dataRecords
        ];
    }

    private function getLatestPosPantau()
    {
        return PosPantau::with(['kabupaten'])
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($pos) {
                return [
                    'nama_pos' => $pos->nama_pos,
                    'jenis_pos' => $pos->jenis_pos,
                    'kabupaten' => $pos->kabupaten->nama ?? 'N/A',
                    'created_at' => $pos->created_at
                ];
            });
    }

    private function getQuickAlerts()
    {
        $alerts = [];

        // Pos without recent data (>30 days)
        $posWithoutData = PosPantau::whereDoesntHave('dataCurahHujan', function ($query) {
            $query->where('tanggal', '>=', now()->subDays(30));
        })
        ->whereDoesntHave('dataTinggiMukaAir', function ($query) {
            $query->where('tanggal', '>=', now()->subDays(30));
        })
        ->whereDoesntHave('dataKlimatologi', function ($query) {
            $query->where('tanggal', '>=', now()->subDays(30));
        })
        ->count();

        if ($posWithoutData > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => '📍',
                'message' => "Pos tanpa data > 30 hari: {$posWithoutData} pos"
            ];
        }

        // Inactive pos
        $inactivePos = PosPantau::where('status', '!=', 'aktif')->count();
        if ($inactivePos > 0) {
            $alerts[] = [
                'type' => 'info', 
                'icon' => '⚠️',
                'message' => "Pos tidak aktif: {$inactivePos} pos"
            ];
        }

        // Recent data entries (last 7 days)
        $recentDataCount = DataCurahHujan::where('created_at', '>=', now()->subDays(7))->count() +
                          DataTinggiMukaAir::where('created_at', '>=', now()->subDays(7))->count() +
                          DataKlimatologi::where('created_at', '>=', now()->subDays(7))->count();
        
        if ($recentDataCount > 0) {
            $alerts[] = [
                'type' => 'success',
                'icon' => '📈',
                'message' => "Data baru 7 hari terakhir: {$recentDataCount} record"
            ];
        }

        return $alerts;
    }

    private function getPosByKabupaten()
    {
        $posCounts = PosPantau::select('kabupaten_id', DB::raw('count(*) as total'))
            ->whereNotNull('kabupaten_id')
            ->groupBy('kabupaten_id')
            ->pluck('total', 'kabupaten_id');

        if ($posCounts->isEmpty()) {
            return collect();
        }

        // Get kabupaten names for the counted IDs
        $kabupatenIds = $posCounts->keys();
        $kabupatenNames = \App\Models\Kabupaten::whereIn('id', $kabupatenIds)
            ->pluck('nama', 'id');

        return $posCounts->map(function ($total, $kabupatenId) use ($kabupatenNames) {
            return [
                'kabupaten' => $kabupatenNames[$kabupatenId] ?? 'Unknown',
                'total' => $total
            ];
        })
        ->sortByDesc('total')
        ->take(10)
        ->values();
    }
}
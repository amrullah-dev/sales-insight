<?php

namespace App\Http\Controllers;

use App\Services\Analytics\TrendAnalysisService;
use Illuminate\View\View;

class TrendAnalyticsController extends Controller
{
    /**
     * Display historical monthly sales trend analysis.
     */
    public function index(TrendAnalysisService $trendAnalysisService): View
    {
        $monthlyTrend = $trendAnalysisService->getMonthlyTrend();
        $monthlyTrend = array_map(function (array $month): array {
            $month['status'] = $this->statusFor($month);
            return $month;
        }, $monthlyTrend);
        $averageMonthlyUnits = $trendAnalysisService->getAverageMonthlyUnits();
        $highestMonth = $trendAnalysisService->getHighestSalesMonth();
        $lowestMonth = $trendAnalysisService->getLowestSalesMonth();
        $latestMonth = ! empty($monthlyTrend) ? $monthlyTrend[array_key_last($monthlyTrend)] : null;
        $firstMonth = $monthlyTrend[0] ?? null;

        $latestChange = $latestMonth['change_percentage'] ?? null;
        $latestStatus = $this->statusFor($latestMonth);

        return view('trends.index', [
            'monthlyTrend' => $monthlyTrend,
            'averageMonthlyUnits' => $averageMonthlyUnits,
            'highestMonth' => $highestMonth,
            'lowestMonth' => $lowestMonth,
            'latestMonth' => $latestMonth,
            'latestChange' => $latestChange,
            'latestStatus' => $latestStatus,
            'dateRange' => $firstMonth && $latestMonth
                ? $firstMonth['label_id'] . ' — ' . $latestMonth['label_id']
                : 'Belum tersedia',
            'chartData' => [
                'labels' => array_column($monthlyTrend, 'label_id'),
                'units' => array_column($monthlyTrend, 'units'),
            ],
        ]);
    }

    /** @return array{label: string, icon: string, class: string} */
    private function statusFor(?array $month): array
    {
        if (! $month || $month['change_percentage'] === null) {
            return ['label' => '—', 'icon' => '—', 'class' => 'text-slate-500'];
        }

        if (($month['previous_units'] ?? null) === 0) {
            return ['label' => 'Tidak tersedia', 'icon' => '—', 'class' => 'text-slate-500'];
        }

        if ($month['change_percentage'] > 5) {
            return ['label' => 'Meningkat', 'icon' => '↑', 'class' => 'text-emerald-700'];
        }

        if ($month['change_percentage'] < -5) {
            return ['label' => 'Menurun', 'icon' => '↓', 'class' => 'text-rose-700'];
        }

        return ['label' => 'Stabil', 'icon' => '→', 'class' => 'text-amber-700'];
    }
}

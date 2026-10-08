<?php

namespace App\Http\Controllers;

use App\Services\Analytics\ProductPerformanceService;
use App\Services\Analytics\SalesMetricsService;
use App\Services\Analytics\TrendAnalysisService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the executive sales overview powered by analytics services.
     */
    public function index(
        SalesMetricsService $metricsService,
        ProductPerformanceService $productPerformanceService,
        TrendAnalysisService $trendAnalysisService
    ): View {
        $summary = $metricsService->getSummaryMetrics();
        $bestProduct = $productPerformanceService->getBestSellingProduct();
        $lowestProduct = $productPerformanceService->getLowestSellingProduct();
        $topProducts = $productPerformanceService->getTopProducts(5);
        $bottomProducts = $productPerformanceService->getBottomProducts(5);
        $monthlyTrend = $trendAnalysisService->getMonthlyTrend();

        $dateRangeFormatted = ($summary['earliest_date_id'] && $summary['latest_date_id'])
            ? "{$summary['earliest_date_id']} — {$summary['latest_date_id']}"
            : 'Belum ada data';

        // Generate concise, data-driven analytical insights
        $insights = $this->generateInsights($summary, $bestProduct, $lowestProduct);

        // Prepare chart datasets for Chart.js
        $monthlyLabels = array_column($monthlyTrend, 'label_id');
        $monthlyUnits = array_column($monthlyTrend, 'units');

        $topProductLabels = $topProducts->pluck('name')->toArray();
        $topProductUnits = $topProducts->pluck('total_units')->toArray();

        $bottomProductLabels = $bottomProducts->pluck('name')->toArray();
        $bottomProductUnits = $bottomProducts->pluck('total_units')->toArray();

        return view('dashboard', [
            'productCount' => $summary['product_count'],
            'salesRecordCount' => $summary['record_count'],
            'dateRangeFormatted' => $dateRangeFormatted,
            'totalUnits' => $summary['total_units'],
            'averageMonthlyUnits' => $summary['average_monthly_units'],
            'bestMonth' => $summary['best_month'],
            'lowestMonth' => $summary['lowest_month'],
            'bestProduct' => $bestProduct,
            'lowestProduct' => $lowestProduct,
            'topProducts' => $topProducts,
            'bottomProducts' => $bottomProducts,
            'monthlyTrend' => $monthlyTrend,
            'insights' => $insights,
            'chartData' => [
                'monthly' => [
                    'labels' => $monthlyLabels,
                    'units' => $monthlyUnits,
                ],
                'topProducts' => [
                    'labels' => $topProductLabels,
                    'units' => $topProductUnits,
                ],
                'bottomProducts' => [
                    'labels' => $bottomProductLabels,
                    'units' => $bottomProductUnits,
                ],
            ],
        ]);
    }

    /**
     * Generate concise, human-readable analytical observations in natural Bahasa Indonesia.
     *
     * @param array<string, mixed> $summary
     * @param array<string, mixed>|null $bestProduct
     * @param array<string, mixed>|null $lowestProduct
     * @return list<string>
     */
    private function generateInsights(array $summary, ?array $bestProduct, ?array $lowestProduct): array
    {
        if ($summary['record_count'] === 0) {
            return ['Belum ada data penjualan yang tercatat dalam sistem.'];
        }

        $insights = [];

        // Insight 1: Produk Terlaris
        if ($bestProduct) {
            $units = number_format($bestProduct['total_units'], 0, ',', '.');
            $status = $bestProduct['trend']['status_label'] ?? 'Stabil';
            $insights[] = "Produk terlaris adalah <strong>{$bestProduct['name']}</strong> dengan total volume <strong>{$units} unit</strong> terjual (tren: {$status}).";
        }

        // Insight 2: Puncak dan Penurunan Penjualan Bulanan
        if (!empty($summary['best_month'])) {
            $bestLabel = $summary['best_month']['label_id'] ?? $summary['best_month']['label'];
            $bestUnits = number_format($summary['best_month']['units'], 0, ',', '.');
            $insights[] = "Bulan Penjualan Tertinggi tercatat pada <strong>{$bestLabel}</strong> dengan total <strong>{$bestUnits} unit</strong>.";
        }

        if (!empty($summary['lowest_month'])) {
            $lowestLabel = $summary['lowest_month']['label_id'] ?? $summary['lowest_month']['label'];
            $lowestUnits = number_format($summary['lowest_month']['units'], 0, ',', '.');
            $insights[] = "Bulan Penjualan Terendah terjadi pada <strong>{$lowestLabel}</strong> sebanyak <strong>{$lowestUnits} unit</strong>.";
        }

        // Insight 3: Rata-rata dan Penjualan Terendah
        if ($lowestProduct && $summary['average_monthly_units'] > 0) {
            $avgUnits = number_format($summary['average_monthly_units'], 1, ',', '.');
            $lowestUnits = number_format($lowestProduct['total_units'], 0, ',', '.');
            $insights[] = "Rata-rata penjualan mencapai <strong>{$avgUnits} unit/bulan</strong>, sementara produk dengan volume terendah adalah <strong>{$lowestProduct['name']}</strong> ({$lowestUnits} unit).";
        }

        return $insights;
    }
}

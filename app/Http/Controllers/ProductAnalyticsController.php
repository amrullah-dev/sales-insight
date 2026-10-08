<?php

namespace App\Http\Controllers;

use App\Services\Analytics\ProductPerformanceService;
use App\Services\Analytics\SalesMetricsService;
use Illuminate\View\View;

class ProductAnalyticsController extends Controller
{
    /**
     * Display product analytics overview, rankings, and trends.
     */
    public function index(
        ProductPerformanceService $productPerformanceService,
        SalesMetricsService $salesMetricsService
    ): View {
        $summary = $salesMetricsService->getSummaryMetrics();

        // Get all products with performance metrics, ordered by total units sold descending
        $products = $productPerformanceService->getProductPerformanceList()
            ->sortByDesc('total_units')
            ->values();

        $hasData = $summary['record_count'] > 0 && $products->isNotEmpty() && $products->first()['total_units'] > 0;

        // Key product highlights
        $bestProduct = $hasData ? $products->first() : null;
        $lowestProduct = $hasData ? $products->last() : null;

        $topGrowingProduct = $hasData
            ? $products->where('trend.status', 'Growing')->sortByDesc('trend.change_percentage')->first()
            : null;

        $topDecliningProduct = $hasData
            ? $products->where('trend.status', 'Declining')->sortBy('trend.change_percentage')->first()
            : null;

        $dateRangeFormatted = ($summary['earliest_date_id'] && $summary['latest_date_id'])
            ? "{$summary['earliest_date_id']} — {$summary['latest_date_id']}"
            : 'Belum ada data';

        // Prepare chart payload (top 10 products ordered by total units)
        $chartLabels = $products->pluck('name')->toArray();
        $chartUnits = $products->pluck('total_units')->toArray();

        return view('products.index', [
            'products' => $products,
            'productCount' => $summary['product_count'],
            'salesRecordCount' => $summary['record_count'],
            'dateRangeFormatted' => $dateRangeFormatted,
            'hasData' => $hasData,
            'bestProduct' => $bestProduct,
            'lowestProduct' => $lowestProduct,
            'topGrowingProduct' => $topGrowingProduct,
            'topDecliningProduct' => $topDecliningProduct,
            'chartData' => [
                'labels' => $chartLabels,
                'units' => $chartUnits,
            ],
        ]);
    }
}

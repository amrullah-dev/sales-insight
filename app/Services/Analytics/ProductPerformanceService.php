<?php

namespace App\Services\Analytics;

use App\Models\Product;
use App\Models\SalesRecord;
use Illuminate\Support\Collection;

class ProductPerformanceService
{
    /**
     * Threshold percentages defining trend direction.
     * Increase > +5%  => Growing
     * Decrease < -5%  => Declining
     * Between -5% & +5% => Stable
     */
    public const TREND_GROWING_THRESHOLD = 5.0;
    public const TREND_DECLINING_THRESHOLD = -5.0;

    /**
     * Retrieve products with aggregated total units and average monthly units.
     * Products with zero sales are included with 0 units.
     *
     * @return Collection<int, array{id: int, name: string, category: string|null, total_units: int, average_monthly_units: float, active_months: int, trend: array<string, mixed>}>
     */
    public function getProductPerformanceList(?string $startDate = null, ?string $endDate = null): Collection
    {
        $products = Product::query()
            ->with(['salesRecords' => function ($query) use ($startDate, $endDate) {
                $query->filterRange($startDate, $endDate)->orderBy('sale_date', 'asc');
            }])
            ->get();

        return $products->map(function (Product $product) {
            $records = $product->salesRecords;
            $totalUnits = (int) $records->sum('quantity');

            $distinctMonths = $records->map(function ($record) {
                return substr($record->sale_date->toDateString(), 0, 7);
            })->unique()->count();

            $averageMonthly = $distinctMonths > 0 ? round($totalUnits / $distinctMonths, 1) : 0.0;

            $trend = $this->classifyProductTrendFromRecords($records);

            return [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category,
                'total_units' => $totalUnits,
                'average_monthly_units' => $averageMonthly,
                'active_months' => $distinctMonths,
                'trend' => $trend,
            ];
        });
    }

    /**
     * Get Top N products ranked by total units sold descending.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getTopProducts(int $limit = 5, ?string $startDate = null, ?string $endDate = null): Collection
    {
        return $this->getProductPerformanceList($startDate, $endDate)
            ->sortByDesc('total_units')
            ->values()
            ->take($limit);
    }

    /**
     * Get Bottom N products ranked by total units sold ascending.
     * Includes products with zero sales at the top of the bottom list.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getBottomProducts(int $limit = 5, ?string $startDate = null, ?string $endDate = null): Collection
    {
        return $this->getProductPerformanceList($startDate, $endDate)
            ->sortBy('total_units')
            ->values()
            ->take($limit);
    }

    /**
     * Get the single best-selling product.
     *
     * @return array<string, mixed>|null
     */
    public function getBestSellingProduct(?string $startDate = null, ?string $endDate = null): ?array
    {
        return $this->getTopProducts(1, $startDate, $endDate)->first();
    }

    /**
     * Get the single lowest-selling product.
     *
     * @return array<string, mixed>|null
     */
    public function getLowestSellingProduct(?string $startDate = null, ?string $endDate = null): ?array
    {
        return $this->getBottomProducts(1, $startDate, $endDate)->first();
    }

    /**
     * Classify product sales trend by comparing two comparable chronological periods.
     * Business Rule:
     * - Divides chronological monthly records into equal halves: previous period and recent period.
     * - MoM/Period Change = ((recent_units - previous_units) / previous_units) * 100
     * - Change > +5% => Growing
     * - Change < -5% => Declining
     * - Otherwise => Stable
     *
     * @return array{status: 'Growing'|'Stable'|'Declining', change_percentage: float|null, previous_units: int, recent_units: int}
     */
    public function classifyProductTrend(int $productId, ?string $startDate = null, ?string $endDate = null): array
    {
        $records = SalesRecord::query()
            ->where('product_id', $productId)
            ->filterRange($startDate, $endDate)
            ->orderBy('sale_date', 'asc')
            ->get();

        return $this->classifyProductTrendFromRecords($records);
    }

    /**
     * Internal calculation of product trend from an ordered collection of records.
     *
     * @param Collection<int, SalesRecord> $records
     * @return array{status: 'Growing'|'Stable'|'Declining', change_percentage: float|null, previous_units: int, recent_units: int}
     */
    private function classifyProductTrendFromRecords(Collection $records): array
    {
        $count = $records->count();

        // Not enough data points to establish a meaningful trend
        if ($count < 2) {
            $units = (int) $records->sum('quantity');
            return [
                'status' => 'Stable',
                'status_label' => 'Stabil',
                'change_percentage' => 0.0,
                'previous_units' => $units,
                'recent_units' => $units,
            ];
        }

        // Divide chronological periods into two comparable equal-length halves
        $half = (int) floor($count / 2);
        $previousPeriod = $records->slice(0, $half);
        $recentPeriod = $records->slice($count - $half);

        $previousUnits = (int) $previousPeriod->sum('quantity');
        $recentUnits = (int) $recentPeriod->sum('quantity');

        // Handle edge case: previous period has 0 units
        if ($previousUnits === 0) {
            if ($recentUnits > 0) {
                return [
                    'status' => 'Growing',
                    'status_label' => 'Meningkat',
                    'change_percentage' => 100.0,
                    'previous_units' => 0,
                    'recent_units' => $recentUnits,
                ];
            }

            return [
                'status' => 'Stable',
                'status_label' => 'Stabil',
                'change_percentage' => 0.0,
                'previous_units' => 0,
                'recent_units' => 0,
            ];
        }

        $changePercentage = round((($recentUnits - $previousUnits) / $previousUnits) * 100, 1);

        $status = match (true) {
            $changePercentage > self::TREND_GROWING_THRESHOLD => 'Growing',
            $changePercentage < self::TREND_DECLINING_THRESHOLD => 'Declining',
            default => 'Stable',
        };

        $statusLabel = match ($status) {
            'Growing' => 'Meningkat',
            'Declining' => 'Menurun',
            default => 'Stabil',
        };

        return [
            'status' => $status,
            'status_label' => $statusLabel,
            'change_percentage' => $changePercentage,
            'previous_units' => $previousUnits,
            'recent_units' => $recentUnits,
        ];
    }
}

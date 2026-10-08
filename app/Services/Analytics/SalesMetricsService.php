<?php

namespace App\Services\Analytics;

use App\Models\Product;
use App\Models\SalesRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesMetricsService
{
    /**
     * Get the total sales quantity (units sold) across filtered records.
     */
    public function getTotalUnits(?string $startDate = null, ?string $endDate = null, ?int $productId = null): int
    {
        return (int) SalesRecord::query()
            ->filterRange($startDate, $endDate, $productId)
            ->sum('quantity');
    }

    /**
     * Get the total count of registered products.
     */
    public function getProductCount(): int
    {
        return Product::count();
    }

    /**
     * Get the total number of sales records matching filters.
     */
    public function getSalesRecordCount(?string $startDate = null, ?string $endDate = null, ?int $productId = null): int
    {
        return SalesRecord::query()
            ->filterRange($startDate, $endDate, $productId)
            ->count();
    }

    /**
     * Calculate the average units sold per active month.
     * Calculated as: Total Units / Count of distinct year-month periods.
     * Prevents division-by-zero by returning 0.0 when no periods exist.
     */
    public function getAverageMonthlyUnits(?string $startDate = null, ?string $endDate = null, ?int $productId = null): float
    {
        $totalUnits = $this->getTotalUnits($startDate, $endDate, $productId);
        $distinctMonths = $this->getDistinctMonthCount($startDate, $endDate, $productId);

        if ($distinctMonths === 0) {
            return 0.0;
        }

        return round($totalUnits / $distinctMonths, 1);
    }

    /**
     * Count the number of distinct year-month periods in the filtered dataset.
     */
    public function getDistinctMonthCount(?string $startDate = null, ?string $endDate = null, ?int $productId = null): int
    {
        return SalesRecord::query()
            ->filterRange($startDate, $endDate, $productId)
            ->selectRaw('substr(sale_date, 1, 7) as period')
            ->distinct()
            ->pluck('period')
            ->count();
    }

    /**
     * Identify the highest-volume sales month.
     * Returns null if dataset contains no records.
     *
     * @return array{period: string, year: int, month: int, label: string, units: int}|null
     */
    public function getBestMonth(?string $startDate = null, ?string $endDate = null, ?int $productId = null): ?array
    {
        $record = SalesRecord::query()
            ->filterRange($startDate, $endDate, $productId)
            ->selectRaw('substr(sale_date, 1, 7) as period, sum(quantity) as total_units')
            ->groupBy('period')
            ->orderByDesc('total_units')
            ->first();

        if (! $record) {
            return null;
        }

        return $this->formatMonthPeriod($record->period, (int) $record->total_units);
    }

    /**
     * Identify the lowest-volume sales month.
     * Returns null if dataset contains no records.
     *
     * @return array{period: string, year: int, month: int, label: string, units: int}|null
     */
    public function getLowestMonth(?string $startDate = null, ?string $endDate = null, ?int $productId = null): ?array
    {
        $record = SalesRecord::query()
            ->filterRange($startDate, $endDate, $productId)
            ->selectRaw('substr(sale_date, 1, 7) as period, sum(quantity) as total_units')
            ->groupBy('period')
            ->orderBy('total_units', 'asc')
            ->first();

        if (! $record) {
            return null;
        }

        return $this->formatMonthPeriod($record->period, (int) $record->total_units);
    }

    public const INDONESIAN_MONTHS = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * Collect summary metrics into an associative array for dashboard consumption.
     *
     * @return array<string, mixed>
     */
    public function getSummaryMetrics(?string $startDate = null, ?string $endDate = null, ?int $productId = null): array
    {
        $query = SalesRecord::query()->filterRange($startDate, $endDate, $productId);
        $earliest = $query->min('sale_date');
        $latest = $query->max('sale_date');

        $formatDateId = function (?string $raw): ?string {
            if (! $raw) {
                return null;
            }
            $dt = Carbon::parse($raw);
            $m = (int) $dt->format('n');
            $y = $dt->format('Y');

            return (self::INDONESIAN_MONTHS[$m] ?? $dt->format('F')) . ' ' . $y;
        };

        return [
            'total_units' => $this->getTotalUnits($startDate, $endDate, $productId),
            'average_monthly_units' => $this->getAverageMonthlyUnits($startDate, $endDate, $productId),
            'product_count' => $this->getProductCount(),
            'record_count' => $this->getSalesRecordCount($startDate, $endDate, $productId),
            'distinct_months' => $this->getDistinctMonthCount($startDate, $endDate, $productId),
            'best_month' => $this->getBestMonth($startDate, $endDate, $productId),
            'lowest_month' => $this->getLowestMonth($startDate, $endDate, $productId),
            'earliest_date' => $earliest ? Carbon::parse($earliest)->format('M Y') : null,
            'latest_date' => $latest ? Carbon::parse($latest)->format('M Y') : null,
            'earliest_date_id' => $formatDateId($earliest),
            'latest_date_id' => $formatDateId($latest),
        ];
    }

    /**
     * Helper to format a YYYY-MM period string into an informative data structure.
     *
     * @return array{period: string, year: int, month: int, label: string, label_id: string, units: int}
     */
    private function formatMonthPeriod(string $period, int $units): array
    {
        $date = Carbon::createFromFormat('Y-m', $period)->startOfMonth();
        $month = (int) $date->format('n');
        $year = (int) $date->format('Y');

        return [
            'period' => $period,
            'year' => $year,
            'month' => $month,
            'label' => $date->format('M Y'),
            'label_id' => (self::INDONESIAN_MONTHS[$month] ?? $date->format('F')) . ' ' . $year,
            'units' => $units,
        ];
    }
}

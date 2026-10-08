<?php

namespace App\Services\Analytics;

use App\Models\SalesRecord;
use Carbon\Carbon;

class TrendAnalysisService
{
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
     * Retrieve chronological monthly sales data with Month-over-Month (MoM) change percentage.
     * Structured for direct consumption by views and Chart.js datasets.
     *
     * @return array<int, array{period: string, label: string, label_id: string, year: int, month: int, units: int, previous_units: int|null, change_percentage: float|null}>
     */
    public function getMonthlyTrend(?string $startDate = null, ?string $endDate = null, ?int $productId = null): array
    {
        $monthlyRecords = SalesRecord::query()
            ->filterRange($startDate, $endDate, $productId)
            ->selectRaw('substr(sale_date, 1, 7) as period, sum(quantity) as total_units')
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();

        $trendData = [];
        $previousUnits = null;

        foreach ($monthlyRecords as $record) {
            $period = $record->period;
            $units = (int) $record->total_units;
            $date = Carbon::createFromFormat('Y-m', $period)->startOfMonth();
            $month = (int) $date->format('n');
            $year = (int) $date->format('Y');

            // Calculate MoM percentage change
            $changePercentage = null;

            if ($previousUnits !== null) {
                if ($previousUnits === 0) {
                    $changePercentage = $units > 0 ? 100.0 : 0.0;
                } else {
                    $changePercentage = round((($units - $previousUnits) / $previousUnits) * 100, 1);
                }
            }

            $trendData[] = [
                'period' => $period,
                'label' => $date->format('M Y'),
                'label_id' => (self::INDONESIAN_MONTHS[$month] ?? $date->format('F')) . ' ' . $year,
                'year' => $year,
                'month' => $month,
                'units' => $units,
                'previous_units' => $previousUnits,
                'change_percentage' => $changePercentage,
            ];

            $previousUnits = $units;
        }

        return $trendData;
    }

    /**
     * Calculate the average monthly units across the chronological series.
     */
    public function getAverageMonthlyUnits(?string $startDate = null, ?string $endDate = null, ?int $productId = null): float
    {
        $monthlyTrend = $this->getMonthlyTrend($startDate, $endDate, $productId);
        $totalMonths = count($monthlyTrend);

        if ($totalMonths === 0) {
            return 0.0;
        }

        $totalUnits = array_sum(array_column($monthlyTrend, 'units'));

        return round($totalUnits / $totalMonths, 1);
    }

    /**
     * Identify the highest sales month in the timeline.
     *
     * @return array{period: string, label: string, year: int, month: int, units: int, change_percentage: float|null}|null
     */
    public function getHighestSalesMonth(?string $startDate = null, ?string $endDate = null, ?int $productId = null): ?array
    {
        $monthlyTrend = $this->getMonthlyTrend($startDate, $endDate, $productId);

        if (empty($monthlyTrend)) {
            return null;
        }

        return collect($monthlyTrend)->sortByDesc('units')->first();
    }

    /**
     * Identify the lowest sales month in the timeline.
     *
     * @return array{period: string, label: string, year: int, month: int, units: int, change_percentage: float|null}|null
     */
    public function getLowestSalesMonth(?string $startDate = null, ?string $endDate = null, ?int $productId = null): ?array
    {
        $monthlyTrend = $this->getMonthlyTrend($startDate, $endDate, $productId);

        if (empty($monthlyTrend)) {
            return null;
        }

        return collect($monthlyTrend)->sortBy('units')->first();
    }
}

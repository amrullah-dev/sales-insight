<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\SalesRecord;
use App\Services\Analytics\SalesMetricsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesMetricsServiceTest extends TestCase
{
    use RefreshDatabase;

    private SalesMetricsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SalesMetricsService();
    }

    public function test_empty_dataset_returns_zero_and_null_safely(): void
    {
        $this->assertEquals(0, $this->service->getTotalUnits());
        $this->assertEquals(0.0, $this->service->getAverageMonthlyUnits());
        $this->assertEquals(0, $this->service->getSalesRecordCount());
        $this->assertEquals(0, $this->service->getDistinctMonthCount());
        $this->assertNull($this->service->getBestMonth());
        $this->assertNull($this->service->getLowestMonth());

        $summary = $this->service->getSummaryMetrics();
        $this->assertEquals(0, $summary['total_units']);
        $this->assertEquals(0.0, $summary['average_monthly_units']);
        $this->assertNull($summary['best_month']);
        $this->assertNull($summary['lowest_month']);
    }

    public function test_total_units_and_average_monthly_units_calculation(): void
    {
        $product = Product::create(['name' => 'Widget Alpha']);

        // Month 1: 100 units
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-01-01', 'quantity' => 100]);
        // Month 2: 200 units
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-02-01', 'quantity' => 200]);
        // Month 3: 150 units
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-03-01', 'quantity' => 150]);

        $this->assertEquals(450, $this->service->getTotalUnits());
        $this->assertEquals(3, $this->service->getDistinctMonthCount());
        // 450 / 3 = 150.0
        $this->assertEquals(150.0, $this->service->getAverageMonthlyUnits());
    }

    public function test_best_and_lowest_month_identification(): void
    {
        $product = Product::create(['name' => 'Widget Beta']);

        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-01-01', 'quantity' => 50]);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-02-01', 'quantity' => 300]);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-03-01', 'quantity' => 120]);

        $best = $this->service->getBestMonth();
        $this->assertNotNull($best);
        $this->assertEquals('2024-02', $best['period']);
        $this->assertEquals(300, $best['units']);
        $this->assertEquals('Feb 2024', $best['label']);

        $lowest = $this->service->getLowestMonth();
        $this->assertNotNull($lowest);
        $this->assertEquals('2024-01', $lowest['period']);
        $this->assertEquals(50, $lowest['units']);
        $this->assertEquals('Jan 2024', $lowest['label']);
    }

    public function test_filter_range_restricts_calculations_correctly(): void
    {
        $product = Product::create(['name' => 'Widget Gamma']);

        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-01-01', 'quantity' => 50]);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-02-01', 'quantity' => 100]);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-03-01', 'quantity' => 200]);

        // Filter for Feb & Mar only
        $total = $this->service->getTotalUnits('2024-02-01', '2024-03-31');
        $this->assertEquals(300, $total);

        $avg = $this->service->getAverageMonthlyUnits('2024-02-01', '2024-03-31');
        $this->assertEquals(150.0, $avg);
    }
}

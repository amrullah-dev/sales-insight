<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\SalesRecord;
use App\Services\Analytics\TrendAnalysisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrendAnalysisServiceTest extends TestCase
{
    use RefreshDatabase;

    private TrendAnalysisService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TrendAnalysisService();
    }

    public function test_empty_dataset_returns_empty_array_and_nulls(): void
    {
        $this->assertEmpty($this->service->getMonthlyTrend());
        $this->assertEquals(0.0, $this->service->getAverageMonthlyUnits());
        $this->assertNull($this->service->getHighestSalesMonth());
        $this->assertNull($this->service->getLowestSalesMonth());
    }

    public function test_monthly_trend_chronological_ordering_and_mom_calculation(): void
    {
        $product = Product::create(['name' => 'Chronos Item']);

        // Insert out of chronological order to verify query sorts correctly
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-03-01', 'quantity' => 150]);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-01-01', 'quantity' => 100]);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-02-01', 'quantity' => 200]);

        $trend = $this->service->getMonthlyTrend();

        $this->assertCount(3, $trend);

        // Month 1: Jan 2024, 100 units, MoM = null
        $this->assertEquals('2024-01', $trend[0]['period']);
        $this->assertEquals('Jan 2024', $trend[0]['label']);
        $this->assertEquals(100, $trend[0]['units']);
        $this->assertNull($trend[0]['change_percentage']);

        // Month 2: Feb 2024, 200 units, MoM = ((200 - 100) / 100) * 100 = +100.0%
        $this->assertEquals('2024-02', $trend[1]['period']);
        $this->assertEquals('Feb 2024', $trend[1]['label']);
        $this->assertEquals(200, $trend[1]['units']);
        $this->assertEquals(100.0, $trend[1]['change_percentage']);

        // Month 3: Mar 2024, 150 units, MoM = ((150 - 200) / 200) * 100 = -25.0%
        $this->assertEquals('2024-03', $trend[2]['period']);
        $this->assertEquals('Mar 2024', $trend[2]['label']);
        $this->assertEquals(150, $trend[2]['units']);
        $this->assertEquals(-25.0, $trend[2]['change_percentage']);
    }

    public function test_mom_with_zero_previous_period_does_not_throw_division_by_zero(): void
    {
        $product = Product::create(['name' => 'Edge Case Item']);

        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-01-01', 'quantity' => 0]);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-02-01', 'quantity' => 100]);

        $trend = $this->service->getMonthlyTrend();

        $this->assertCount(2, $trend);
        $this->assertNull($trend[0]['change_percentage']);
        $this->assertEquals(100.0, $trend[1]['change_percentage']);
    }

    public function test_highest_and_lowest_sales_months(): void
    {
        $product = Product::create(['name' => 'Test Product']);

        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-01-01', 'quantity' => 80]);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-02-01', 'quantity' => 250]);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-03-01', 'quantity' => 120]);

        $highest = $this->service->getHighestSalesMonth();
        $this->assertNotNull($highest);
        $this->assertEquals('2024-02', $highest['period']);
        $this->assertEquals(250, $highest['units']);

        $lowest = $this->service->getLowestSalesMonth();
        $this->assertNotNull($lowest);
        $this->assertEquals('2024-01', $lowest['period']);
        $this->assertEquals(80, $lowest['units']);

        $average = $this->service->getAverageMonthlyUnits();
        // (80 + 250 + 120) / 3 = 450 / 3 = 150.0
        $this->assertEquals(150.0, $average);
    }
}

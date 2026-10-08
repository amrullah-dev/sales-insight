<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\SalesRecord;
use App\Services\Analytics\ProductPerformanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPerformanceServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductPerformanceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProductPerformanceService();
    }

    public function test_top_and_bottom_products_ranking(): void
    {
        $p1 = Product::create(['name' => 'Top Item']);
        $p2 = Product::create(['name' => 'Middle Item']);
        $p3 = Product::create(['name' => 'Bottom Item']);
        $p4 = Product::create(['name' => 'Zero Sales Item']); // Zero sales

        SalesRecord::create(['product_id' => $p1->id, 'sale_date' => '2024-01-01', 'quantity' => 500]);
        SalesRecord::create(['product_id' => $p2->id, 'sale_date' => '2024-01-01', 'quantity' => 200]);
        SalesRecord::create(['product_id' => $p3->id, 'sale_date' => '2024-01-01', 'quantity' => 50]);

        $best = $this->service->getBestSellingProduct();
        $this->assertNotNull($best);
        $this->assertEquals($p1->id, $best['id']);
        $this->assertEquals(500, $best['total_units']);

        $lowest = $this->service->getLowestSellingProduct();
        $this->assertNotNull($lowest);
        $this->assertEquals($p4->id, $lowest['id']);
        $this->assertEquals(0, $lowest['total_units']);

        $top2 = $this->service->getTopProducts(2);
        $this->assertCount(2, $top2);
        $this->assertEquals('Top Item', $top2[0]['name']);
        $this->assertEquals('Middle Item', $top2[1]['name']);

        $bottom2 = $this->service->getBottomProducts(2);
        $this->assertCount(2, $bottom2);
        $this->assertEquals('Zero Sales Item', $bottom2[0]['name']);
        $this->assertEquals('Bottom Item', $bottom2[1]['name']);
    }

    public function test_trend_classification_growing(): void
    {
        $product = Product::create(['name' => 'Rising Star']);

        // 4 months: first half (M1, M2) = 100 units; second half (M3, M4) = 150 units (+50% increase)
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-01-01', 'quantity' => 40]);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-02-01', 'quantity' => 60]);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-03-01', 'quantity' => 70]);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-04-01', 'quantity' => 80]);

        $trend = $this->service->classifyProductTrend($product->id);

        $this->assertEquals('Growing', $trend['status']);
        $this->assertEquals(50.0, $trend['change_percentage']);
        $this->assertEquals(100, $trend['previous_units']);
        $this->assertEquals(150, $trend['recent_units']);
    }

    public function test_trend_classification_declining(): void
    {
        $product = Product::create(['name' => 'Falling Star']);

        // 4 months: first half (M1, M2) = 200 units; second half (M3, M4) = 100 units (-50% decrease)
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-01-01', 'quantity' => 100]);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-02-01', 'quantity' => 100]);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-03-01', 'quantity' => 50]);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-04-01', 'quantity' => 50]);

        $trend = $this->service->classifyProductTrend($product->id);

        $this->assertEquals('Declining', $trend['status']);
        $this->assertEquals(-50.0, $trend['change_percentage']);
    }

    public function test_trend_classification_stable(): void
    {
        $product = Product::create(['name' => 'Steady Eddie']);

        // 4 months: first half = 100 units; second half = 102 units (+2% -> within +/-5%)
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-01-01', 'quantity' => 50]);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-02-01', 'quantity' => 50]);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-03-01', 'quantity' => 51]);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-04-01', 'quantity' => 51]);

        $trend = $this->service->classifyProductTrend($product->id);

        $this->assertEquals('Stable', $trend['status']);
        $this->assertEquals(2.0, $trend['change_percentage']);
    }

    public function test_trend_classification_zero_previous_period(): void
    {
        $product = Product::create(['name' => 'New Release']);

        // first half has 0 units, second half has 50 units
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-01-01', 'quantity' => 0]);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-02-01', 'quantity' => 50]);

        $trend = $this->service->classifyProductTrend($product->id);

        $this->assertEquals('Growing', $trend['status']);
        $this->assertEquals(100.0, $trend['change_percentage']);
    }

    public function test_trend_classification_single_record_defaults_to_stable(): void
    {
        $product = Product::create(['name' => 'Single Record Item']);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-01-01', 'quantity' => 25]);

        $trend = $this->service->classifyProductTrend($product->id);

        $this->assertEquals('Stable', $trend['status']);
        $this->assertEquals(0.0, $trend['change_percentage']);
    }
}

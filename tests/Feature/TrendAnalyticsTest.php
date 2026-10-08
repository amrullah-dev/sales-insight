<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\SalesRecord;
use Database\Seeders\SalesDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrendAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_trends_page_renders_indonesian_analysis_and_monthly_data(): void
    {
        $this->seed(SalesDataSeeder::class);

        $response = $this->get(route('trends.index'));

        $response->assertOk();
        $response->assertSee('Analisis Tren Penjualan');
        $response->assertSee('Tren Penjualan Bulanan');
        $response->assertSee('Rata-rata Unit per Bulan');
        $response->assertSee('Januari 2024');
        $response->assertSee('Desember 2025');
        $response->assertSee('Penjualan Tertinggi');
        $response->assertSee('Penjualan Terendah');
        $response->assertSee('id="monthlySalesTrendChart"', false);
    }

    public function test_trends_page_renders_mom_and_first_month_safely(): void
    {
        $product = Product::create(['name' => 'Trend Item']);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-01-01', 'quantity' => 100]);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-02-01', 'quantity' => 110]);

        $response = $this->get(route('trends.index'));

        $response->assertOk();
        $response->assertSee('10,0%');
        $response->assertSee('Januari 2024');
        $response->assertSee('—');
        $response->assertSee('Meningkat');
    }

    public function test_zero_previous_month_is_not_rendered_as_nan_or_infinity(): void
    {
        $product = Product::create(['name' => 'Zero Trend Item']);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-01-01', 'quantity' => 0]);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-02-01', 'quantity' => 100]);

        $response = $this->get(route('trends.index'));

        $response->assertOk();
        $response->assertSee('Tidak tersedia');
        $response->assertDontSee('NaN');
        $response->assertDontSee('Infinity');
    }

    public function test_empty_and_one_month_datasets_are_handled_safely(): void
    {
        $emptyResponse = $this->get(route('trends.index'));
        $emptyResponse->assertOk();
        $emptyResponse->assertSee('Belum ada data penjualan.');
        $emptyResponse->assertDontSee('monthlySalesTrendChart', false);

        $product = Product::create(['name' => 'Single Month Item']);
        SalesRecord::create(['product_id' => $product->id, 'sale_date' => '2024-03-01', 'quantity' => 25]);

        $singleMonthResponse = $this->get(route('trends.index'));
        $singleMonthResponse->assertOk();
        $singleMonthResponse->assertSee('Maret 2024');
        $singleMonthResponse->assertSee('Belum tersedia');
        $singleMonthResponse->assertDontSee('NaN');
    }
}

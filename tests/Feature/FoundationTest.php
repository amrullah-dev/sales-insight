<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\SalesRecord;
use Database\Seeders\SalesDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SalesDataSeeder::class);
    }

    /**
     * Test the dashboard route returns 200 and contains live dataset information in Bahasa Indonesia.
     */
    public function test_dashboard_renders_with_database_values(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dasbor');
        $response->assertSee('Status Data Penjualan');
        $response->assertSee('Produk');
        $response->assertSee('Data Penjualan');
        $response->assertSee('Rentang Waktu');
        $response->assertSee('Januari 2024 — Desember 2025');
        $response->assertSee('Total Unit Terjual');
        $response->assertSee('Rata-rata Unit per Bulan');
        $response->assertSee('Bulan Penjualan Tertinggi');
        $response->assertSee('Bulan Penjualan Terendah');
        $response->assertSee('Produk Terlaris');
    }

    /**
     * Test products placeholder page renders successfully in Bahasa Indonesia.
     */
    public function test_products_page_renders_successfully(): void
    {
        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertSee('Analisis Produk');
        $response->assertSee('Performa Produk');
    }

    /**
     * Test trends placeholder page renders successfully in Bahasa Indonesia.
     */
    public function test_trends_page_renders_successfully(): void
    {
        $response = $this->get(route('trends.index'));

        $response->assertStatus(200);
        $response->assertSee('Analisis Tren Penjualan');
        $response->assertSee('Tren Penjualan Bulanan');
    }

    /**
     * Test models and relationship functionality.
     */
    public function test_product_and_sales_record_relationships(): void
    {
        $product = Product::first();
        $this->assertNotNull($product);
        $this->assertGreaterThan(0, $product->salesRecords()->count());

        $record = SalesRecord::first();
        $this->assertNotNull($record);
        $this->assertInstanceOf(Product::class, $record->product);
        $this->assertIsInt($record->quantity);
        $this->assertEquals(10, Product::count());
        $this->assertEquals(240, SalesRecord::count());
    }
}

<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\SalesRecord;
use Database\Seeders\SalesDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_analytics_page_returns_http_200_and_renders_components(): void
    {
        $this->seed(SalesDataSeeder::class);

        $response = $this->get(route('products.index'));

        $response->assertStatus(200);

        // Header & section titles
        $response->assertSee('Analisis Produk');
        $response->assertSee('Performa Produk');
        $response->assertSee('Perbandingan Produk');

        // Product names from seed data
        $response->assertSee('Apex Wireless Noise-Canceling Headphones');
        $response->assertSee('Nova Smart Fitness Tracker');
        $response->assertSee('Legacy USB 2.0 Multi-Hub');
        $response->assertSee('Precision Stylus Pen Refill Pack');

        // Summary highlights
        $response->assertSee('Produk Terlaris');
        $response->assertSee('Penjualan Terendah');
        $response->assertSee('Produk Meningkat');
        $response->assertSee('Produk Menurun');

        // Trend status labels
        $response->assertSee('Meningkat');
        $response->assertSee('Stabil');
        $response->assertSee('Menurun');

        // Chart canvas is rendered
        $response->assertSee('id="productsComparisonChart"', false);
    }

    public function test_no_crud_actions_exist_on_product_analytics_page(): void
    {
        $this->seed(SalesDataSeeder::class);

        $response = $this->get(route('products.index'));

        $response->assertStatus(200);

        // Ensure no CRUD controls or forms
        $response->assertDontSee('Tambah Produk');
        $response->assertDontSee('Edit Produk');
        $response->assertDontSee('Hapus');
        $response->assertDontSee('form action=', false);
        $response->assertDontSee('method="POST"', false);
    }

    public function test_empty_sales_data_is_handled_safely(): void
    {
        // No seed data -> empty database
        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertSee('Belum ada data produk.');
        $response->assertDontSee('id="productsComparisonChart"', false);
    }

    public function test_products_exist_without_sales_records_handled_safely(): void
    {
        // Create products without sales records
        Product::create(['name' => 'Demo Product Standalone']);

        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertSee('Belum ada data penjualan untuk dianalisis.');
        $response->assertDontSee('id="productsComparisonChart"', false);
    }
}

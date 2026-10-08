<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\SalesRecord;
use Database\Seeders\SalesDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardVisualizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_all_phase3_visual_components(): void
    {
        $this->seed(SalesDataSeeder::class);

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);

        // 4 KPI Cards
        $response->assertSee('Total Unit Terjual');
        $response->assertSee('Rata-rata Unit per Bulan');
        $response->assertSee('Produk Terlaris');
        $response->assertSee('Bulan Penjualan Tertinggi');

        // Insight section
        $response->assertSee('Insight Penjualan');
        $response->assertSee('Bulan Penjualan Terendah');

        // Chart titles and canvases
        $response->assertSee('Tren Penjualan Bulanan');
        $response->assertSee('id="monthlyTrendChart"', false);
        $response->assertSee('id="topProductsChart"', false);
        $response->assertSee('id="bottomProductsChart"', false);

        // Check Top & Bottom rankings sections
        $response->assertSee('Produk dengan Penjualan Terendah');
        $response->assertSee('Top 5');
        $response->assertSee('Bottom 5');
    }

    public function test_dashboard_handles_empty_dataset_gracefully(): void
    {
        // Without running seeder (empty database)
        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Belum ada data penjualan.');
        $response->assertDontSee('id="monthlyTrendChart"', false);
    }
}

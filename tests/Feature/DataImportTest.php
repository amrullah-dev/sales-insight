<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\SalesRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class DataImportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test halaman import dapat diakses.
     */
    public function test_import_page_is_accessible(): void
    {
        $response = $this->get(route('import.show'));

        $response->assertStatus(200);
        $response->assertSee('Impor Data Penjualan');
        $response->assertSee('Format CSV');
    }

    /**
     * Test template CSV dapat diunduh.
     */
    public function test_template_download_returns_csv(): void
    {
        $response = $this->get(route('import.template'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
    }

    /**
     * Test upload file bukan CSV ditolak.
     */
    public function test_import_non_csv_file_is_rejected(): void
    {
        $file = UploadedFile::fake()->create('data.xlsx', 100);

        $response = $this->post(route('import.store'), [
            'csv_file' => $file,
        ]);

        $response->assertSessionHasErrors('csv_file');
        $this->assertEquals(0, SalesRecord::count());
    }

    /**
     * Test upload file CSV kosong ditolak.
     */
    public function test_import_empty_csv_file(): void
    {
        $file = UploadedFile::fake()->createWithContent('empty.csv', '');

        $response = $this->post(route('import.store'), [
            'csv_file' => $file,
        ]);

        $response->assertSessionHas('error');
    }

    /**
     * Test CSV dengan header tidak valid ditolak.
     */
    public function test_import_csv_with_invalid_headers(): void
    {
        $csv = "nama_produk,kategori,tanggal,qty\n";
        $csv .= "Laptop,Elektronik,2024-01-15,5\n";

        $file = UploadedFile::fake()->createWithContent('test.csv', $csv);

        $response = $this->post(route('import.store'), [
            'csv_file' => $file,
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(0, SalesRecord::count());
    }

    /**
     * Test CSV dengan baris data tidak valid (product_name kosong).
     */
    public function test_import_csv_with_missing_product_name(): void
    {
        $csv = "product_name,category,sale_date,quantity\n";
        $csv .= ",Elektronik,2024-01-15,5\n";

        $file = UploadedFile::fake()->createWithContent('test.csv', $csv);

        $response = $this->post(route('import.store'), [
            'csv_file' => $file,
        ]);

        $response->assertSessionHas('error');
        $response->assertSessionHas('validation_errors');
        $this->assertEquals(0, SalesRecord::count());
    }

    /**
     * Test CSV dengan baris data tidak valid (sale_date kosong).
     */
    public function test_import_csv_with_missing_sale_date(): void
    {
        $csv = "product_name,category,sale_date,quantity\n";
        $csv .= "Laptop,Elektronik,,5\n";

        $file = UploadedFile::fake()->createWithContent('test.csv', $csv);

        $response = $this->post(route('import.store'), [
            'csv_file' => $file,
        ]);

        $response->assertSessionHas('error');
        $response->assertSessionHas('validation_errors');
        $this->assertEquals(0, SalesRecord::count());
    }

    /**
     * Test CSV dengan baris data tidak valid (quantity bukan angka).
     */
    public function test_import_csv_with_invalid_quantity(): void
    {
        $csv = "product_name,category,sale_date,quantity\n";
        $csv .= "Laptop,Elektronik,2024-01-15,lima\n";

        $file = UploadedFile::fake()->createWithContent('test.csv', $csv);

        $response = $this->post(route('import.store'), [
            'csv_file' => $file,
        ]);

        $response->assertSessionHas('error');
        $response->assertSessionHas('validation_errors');
        $this->assertEquals(0, SalesRecord::count());
    }

    /**
     * Test CSV dengan baris data tidak valid (quantity negatif).
     */
    public function test_import_csv_with_negative_quantity(): void
    {
        $csv = "product_name,category,sale_date,quantity\n";
        $csv .= "Laptop,Elektronik,2024-01-15,-5\n";

        $file = UploadedFile::fake()->createWithContent('test.csv', $csv);

        $response = $this->post(route('import.store'), [
            'csv_file' => $file,
        ]);

        $response->assertSessionHas('error');
        $response->assertSessionHas('validation_errors');
        $this->assertEquals(0, SalesRecord::count());
    }

    /**
     * Test CSV dengan format tanggal tidak valid.
     */
    public function test_import_csv_with_invalid_date_format(): void
    {
        $csv = "product_name,category,sale_date,quantity\n";
        $csv .= "Laptop,Elektronik,15-01-2024,5\n";

        $file = UploadedFile::fake()->createWithContent('test.csv', $csv);

        $response = $this->post(route('import.store'), [
            'csv_file' => $file,
        ]);

        $response->assertSessionHas('error');
        $response->assertSessionHas('validation_errors');
        $this->assertEquals(0, SalesRecord::count());
    }

    /**
     * Test import dengan multiple errors.
     */
    public function test_import_csv_with_multiple_errors(): void
    {
        $csv = "product_name,category,sale_date,quantity\n";
        $csv .= "Laptop,Elektronik,2024-01-15,5\n";
        $csv .= ",Elektronik,2024-01-16,10\n";
        $csv .= "Mouse,Aksesoris,invalid-date,3\n";

        $file = UploadedFile::fake()->createWithContent('test.csv', $csv);

        $response = $this->post(route('import.store'), [
            'csv_file' => $file,
        ]);

        $response->assertSessionHas('error');
        $this->assertTrue(session()->has('validation_errors'));
        $this->assertEquals(0, SalesRecord::count());
    }

    /**
     * Test file upload form validation.
     */
    public function test_import_file_required(): void
    {
        $response = $this->post(route('import.store'), []);

        $response->assertSessionHasErrors('csv_file');
    }
}

<?php

namespace App\Services\Import;

use App\Models\Product;
use App\Models\SalesRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * Service untuk mengimpor data penjualan dari file CSV.
 *
 * Format CSV yang didukung:
 * - Header: product_name, category, sale_date, quantity
 * - product_name: Wajib (string). Buat produk baru jika belum ada.
 * - category: Opsional (string).
 * - sale_date: Wajib (format YYYY-MM-DD atau YYYY-MM-01).
 * - quantity: Wajib (integer, minimal 0).
 */
class SalesImportService
{
    /**
     * Batas maksimal ukuran file (2MB).
     */
    private const MAX_FILE_SIZE = 2 * 1024 * 1024;

    /**
     * Header kolom yang wajib ada di CSV.
     */
    private const REQUIRED_HEADERS = ['product_name', 'category', 'sale_date', 'quantity'];

    /**
     * Impor file CSV dan simpan ke database.
     *
     * @param UploadedFile $file
     * @return array{success: bool, message: string, records_imported?: int, errors?: list<string>}
     */
    public function import(UploadedFile $file): array
    {
        // Validasi file
        $validation = $this->validateFile($file);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['error'],
            ];
        }

        // Baca dan parse CSV
        $rows = $this->parseCSV($file);
        if (empty($rows)) {
            return [
                'success' => false,
                'message' => 'File CSV kosong atau tidak memiliki data.',
            ];
        }

        // Validasi headers
        $headerValidation = $this->validateHeaders($rows[0]);
        if (!$headerValidation['valid']) {
            return [
                'success' => false,
                'message' => 'Header CSV tidak valid: ' . $headerValidation['error'],
            ];
        }

        // Hapus header row
        array_shift($rows);

        // Validasi dan kumpulkan data
        $validatedRecords = [];
        $errors = [];

        foreach ($rows as $rowIndex => $row) {
            $lineNumber = $rowIndex + 2; // +1 untuk 0-index, +1 untuk header row

            // Skip baris kosong
            if (empty(array_filter($row))) {
                continue;
            }

            $validation = $this->validateRow($row, $lineNumber);
            if (!$validation['valid']) {
                $errors[] = $validation['error'];
                continue;
            }

            $validatedRecords[] = $validation['data'];
        }

        // Jika ada error validasi, kembalikan laporan
        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => 'Ditemukan ' . count($errors) . ' kesalahan validasi:',
                'errors' => $errors,
            ];
        }

        if (empty($validatedRecords)) {
            return [
                'success' => false,
                'message' => 'Tidak ada data valid yang ditemukan dalam file.',
            ];
        }

        // Simpan ke database dalam transaksi
        try {
            $imported = DB::transaction(function () use ($validatedRecords) {
                $count = 0;
                foreach ($validatedRecords as $recordData) {
                    SalesRecord::create($recordData);
                    $count++;
                }
                return $count;
            });

            return [
                'success' => true,
                'message' => "Berhasil mengimpor $imported catatan penjualan.",
                'records_imported' => $imported,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Kesalahan saat menyimpan data: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Validasi file upload.
     *
     * @param UploadedFile $file
     * @return array{valid: bool, error?: string}
     */
    private function validateFile(UploadedFile $file): array
    {
        if (!$file->isValid()) {
            return [
                'valid' => false,
                'error' => 'File upload tidak valid.',
            ];
        }

        if ($file->getClientOriginalExtension() !== 'csv') {
            return [
                'valid' => false,
                'error' => 'Format file harus CSV.',
            ];
        }

        if ($file->getSize() > self::MAX_FILE_SIZE) {
            return [
                'valid' => false,
                'error' => 'Ukuran file terlalu besar (maksimal 2MB).',
            ];
        }

        return ['valid' => true];
    }

    /**
     * Parse file CSV menjadi array baris.
     *
     * @param UploadedFile $file
     * @return list<array<string, string>>
     */
    private function parseCSV(UploadedFile $file): array
    {
        $rows = [];
        $handle = fopen($file->getRealPath(), 'r');

        if (!$handle) {
            return [];
        }

        $headers = null;
        while (($data = fgetcsv($handle)) !== false) {
            if ($headers === null) {
                // Baris pertama adalah header
                $headers = array_map('trim', $data);
                $rows[] = $headers;
            } else {
                // Map data ke header
                $row = [];
                foreach ($headers as $index => $header) {
                    $row[$header] = isset($data[$index]) ? trim($data[$index]) : '';
                }
                $rows[] = $row;
            }
        }

        fclose($handle);
        return $rows;
    }

    /**
     * Validasi header CSV.
     *
     * @param array<string, string> $header
     * @return array{valid: bool, error?: string}
     */
    private function validateHeaders(array $header): array
    {
        $headerKeys = array_keys($header);

        foreach (self::REQUIRED_HEADERS as $requiredHeader) {
            if (!in_array($requiredHeader, $headerKeys, true)) {
                return [
                    'valid' => false,
                    'error' => "Kolom '$requiredHeader' tidak ditemukan.",
                ];
            }
        }

        return ['valid' => true];
    }

    /**
     * Validasi satu baris data.
     *
     * @param array<string, string> $row
     * @param int $lineNumber
     * @return array{valid: bool, error?: string, data?: array}
     */
    private function validateRow(array $row, int $lineNumber): array
    {
        // Validasi product_name
        $productName = $row['product_name'] ?? '';
        if (empty($productName)) {
            return [
                'valid' => false,
                'error' => "Baris $lineNumber: Nama produk wajib diisi.",
            ];
        }

        // Dapatkan atau buat produk
        $product = Product::firstOrCreate(
            ['name' => $productName],
            ['category' => $row['category'] ?? null]
        );

        // Validasi sale_date
        $saleDate = $row['sale_date'] ?? '';
        if (empty($saleDate)) {
            return [
                'valid' => false,
                'error' => "Baris $lineNumber: Tanggal penjualan wajib diisi.",
            ];
        }

        // Parse tanggal
        $parsedDate = $this->parseDate($saleDate);
        if (!$parsedDate) {
            return [
                'valid' => false,
                'error' => "Baris $lineNumber: Format tanggal tidak valid (gunakan YYYY-MM-DD atau YYYY-MM-01).",
            ];
        }

        // Validasi quantity
        $quantityStr = $row['quantity'] ?? '';
        if (empty($quantityStr)) {
            return [
                'valid' => false,
                'error' => "Baris $lineNumber: Jumlah unit wajib diisi.",
            ];
        }

        if (!is_numeric($quantityStr)) {
            return [
                'valid' => false,
                'error' => "Baris $lineNumber: Jumlah unit harus berupa angka.",
            ];
        }

        $quantity = (int) $quantityStr;
        if ($quantity < 0) {
            return [
                'valid' => false,
                'error' => "Baris $lineNumber: Jumlah unit tidak boleh negatif.",
            ];
        }

        return [
            'valid' => true,
            'data' => [
                'product_id' => $product->id,
                'sale_date' => $parsedDate,
                'quantity' => $quantity,
            ],
        ];
    }

    /**
     * Parse string tanggal ke format YYYY-MM-DD.
     *
     * Mendukung format: YYYY-MM-DD atau YYYY-MM-01
     *
     * @param string $dateStr
     * @return string|null
     */
    private function parseDate(string $dateStr): ?string
    {
        // Coba parse sebagai YYYY-MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
            // Validasi tanggal valid
            $parts = explode('-', $dateStr);
            if (checkdate((int) $parts[1], (int) $parts[2], (int) $parts[0])) {
                return $dateStr;
            }
        }

        // Coba parse sebagai YYYY-MM-01 (untuk input yang hanya punya tahun-bulan)
        if (preg_match('/^\d{4}-\d{2}(-01)?$/', $dateStr)) {
            $normalized = preg_replace('/^(\d{4}-\d{2})(-01)?$/', '$1-01', $dateStr);
            $parts = explode('-', $normalized);
            if (checkdate((int) $parts[1], (int) $parts[2], (int) $parts[0])) {
                return $normalized;
            }
        }

        return null;
    }
}

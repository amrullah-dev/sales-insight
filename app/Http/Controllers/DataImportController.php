<?php

namespace App\Http\Controllers;

use App\Services\Import\SalesImportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataImportController extends Controller
{
    /**
     * Tampilkan halaman import data.
     */
    public function show(): View
    {
        return view('import.index');
    }

    /**
     * Proses upload dan import file CSV.
     */
    public function import(Request $request, SalesImportService $importService)
    {
        $validated = $request->validate([
            'csv_file' => 'required|file|mimes:csv|max:2048',
        ], [
            'csv_file.required' => 'Silakan pilih file CSV.',
            'csv_file.file' => 'Upload harus berupa file.',
            'csv_file.mimes' => 'Format file harus CSV.',
            'csv_file.max' => 'Ukuran file terlalu besar (maksimal 2MB).',
        ]);

        $result = $importService->import($validated['csv_file']);

        if ($result['success']) {
            return redirect()->route('import.show')
                ->with('success', $result['message']);
        }

        $errors = $result['errors'] ?? [];
        return redirect()->route('import.show')
            ->with('error', $result['message'])
            ->with('validation_errors', $errors);
    }

    /**
     * Download template CSV contoh.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $filename = 'template_penjualan.csv';

        return response()->streamDownload(function () {
            echo "product_name,category,sale_date,quantity\n";
            echo "Laptop Dell,Elektronik,2024-01-15,5\n";
            echo "Mouse Logitech,Aksesoris,2024-01-16,12\n";
            echo "Keyboard Mekanik,Aksesoris,2024-01-16,3\n";
            echo "Monitor LG,Elektronik,2024-01-20,2\n";
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
}

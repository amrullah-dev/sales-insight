@extends('layouts.app')

@section('header-title', 'Impor Data Penjualan')
@section('header-description', 'Unggah file CSV untuk mengimpor catatan penjualan historis.')

@section('content')
<div class="space-y-6">

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200/80 rounded-xl p-4">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0 mt-0.5">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-emerald-900">Berhasil</h3>
                    <p class="text-sm text-emerald-800 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Error Alert --}}
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200/80 rounded-xl p-4">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0 mt-0.5">
                    <svg class="h-5 w-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c.866 1.5 2.926 2.871 5.303 2.871 2.906 0 5.888-1.08 8.379-3.49m.428 4.395A8.471 8.471 0 0 0 12 20.25c-2.468 0-4.666-.1-6.6-.715m12.318 0a9 9 0 1 1-17.457-5.209" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-rose-900">Kesalahan</h3>
                    <p class="text-sm text-rose-800 mt-0.5">{{ session('error') }}</p>
                    @if(session('validation_errors'))
                        <div class="mt-3 space-y-1">
                            @foreach(session('validation_errors') as $error)
                                <p class="text-xs text-rose-700 font-mono">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Upload Area --}}
    <div class="bg-white border border-slate-200/80 rounded-xl p-8">
        <div class="max-w-2xl mx-auto">
            <form action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- File Input Area --}}
                <div class="space-y-3">
                    <label for="csv_file" class="text-sm font-semibold text-slate-900">File CSV</label>
                    <div class="relative border-2 border-dashed border-slate-300 rounded-lg p-8 text-center hover:border-slate-400 transition-colors cursor-pointer group"
                         id="dropZone">
                        <input type="file" name="csv_file" id="csv_file" accept=".csv" class="hidden" required>

                        <svg class="mx-auto h-10 w-10 text-slate-400 group-hover:text-slate-500 mb-3 transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0-3 3m3-3 3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
                        </svg>

                        <p class="text-sm text-slate-600 font-medium">Seret file ke sini atau klik untuk memilih</p>
                        <p class="text-xs text-slate-400 mt-1">Format CSV, maksimal 2MB</p>

                        <span class="text-xs text-slate-500 mt-2 block" id="fileName">Belum ada file dipilih</span>
                    </div>
                    @error('csv_file')
                        <p class="text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Template Download Link --}}
                <div class="bg-slate-50 rounded-lg p-4 border border-slate-200/80">
                    <p class="text-sm text-slate-600">
                        Belum tahu format yang tepat?
                        <a href="{{ route('import.template') }}" class="font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">
                            Unduh template CSV
                        </a>
                    </p>
                </div>

                {{-- Submit Button --}}
                <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                    <p class="text-xs text-slate-400">Format: product_name, category, sale_date, quantity</p>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" id="submitBtn" disabled>
                        Impor Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Format Information --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Kolom Wajib --}}
        <div class="bg-white border border-slate-200/80 rounded-xl p-5">
            <h3 class="text-sm font-semibold text-slate-900 mb-3">Kolom Wajib</h3>
            <div class="space-y-2 text-sm">
                <div class="flex items-start space-x-2">
                    <span class="text-indigo-600 font-bold mt-0.5">1.</span>
                    <div>
                        <p class="font-mono text-slate-700">product_name</p>
                        <p class="text-xs text-slate-400 mt-0.5">Nama produk. Produk baru akan dibuat otomatis jika belum ada.</p>
                    </div>
                </div>
                <div class="flex items-start space-x-2">
                    <span class="text-indigo-600 font-bold mt-0.5">2.</span>
                    <div>
                        <p class="font-mono text-slate-700">sale_date</p>
                        <p class="text-xs text-slate-400 mt-0.5">Tanggal penjualan. Format: YYYY-MM-DD (misal: 2024-01-15)</p>
                    </div>
                </div>
                <div class="flex items-start space-x-2">
                    <span class="text-indigo-600 font-bold mt-0.5">3.</span>
                    <div>
                        <p class="font-mono text-slate-700">quantity</p>
                        <p class="text-xs text-slate-400 mt-0.5">Jumlah unit. Harus berupa angka positif.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Opsional --}}
        <div class="bg-white border border-slate-200/80 rounded-xl p-5">
            <h3 class="text-sm font-semibold text-slate-900 mb-3">Kolom Opsional</h3>
            <div class="space-y-2 text-sm">
                <div class="flex items-start space-x-2">
                    <span class="text-slate-400 font-bold mt-0.5">-</span>
                    <div>
                        <p class="font-mono text-slate-700">category</p>
                        <p class="text-xs text-slate-400 mt-0.5">Kategori produk. Dapat dikosongkan atau diisi dengan teks deskriptif.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Example Data --}}
    <div class="bg-white border border-slate-200/80 rounded-xl p-5">
        <h3 class="text-sm font-semibold text-slate-900 mb-3">Contoh Data CSV</h3>
        <div class="bg-slate-50 rounded-lg p-4 overflow-x-auto">
            <pre class="text-xs text-slate-700 font-mono leading-relaxed">product_name,category,sale_date,quantity
Laptop Dell,Elektronik,2024-01-15,5
Mouse Logitech,Aksesoris,2024-01-16,12
Keyboard Mekanik,Aksesoris,2024-01-20,3
Monitor LG,Elektronik,2024-02-01,2</pre>
        </div>
    </div>

</div>

{{-- File Input Handler JavaScript --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('csv_file');
        const fileNameDisplay = document.getElementById('fileName');
        const submitBtn = document.getElementById('submitBtn');

        // Handle file selection
        fileInput.addEventListener('change', (e) => {
            updateFileName(e.target.files);
        });

        // Handle drag and drop
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('bg-slate-100');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('bg-slate-100');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('bg-slate-100');
            fileInput.files = e.dataTransfer.files;
            updateFileName(e.dataTransfer.files);
        });

        dropZone.addEventListener('click', () => {
            fileInput.click();
        });

        function updateFileName(files) {
            if (files.length > 0) {
                fileNameDisplay.textContent = files[0].name + ' (' + formatFileSize(files[0].size) + ')';
                fileNameDisplay.classList.remove('text-slate-500');
                fileNameDisplay.classList.add('text-slate-700', 'font-medium');
                submitBtn.disabled = false;
            } else {
                fileNameDisplay.textContent = 'Belum ada file dipilih';
                fileNameDisplay.classList.add('text-slate-500');
                fileNameDisplay.classList.remove('text-slate-700', 'font-medium');
                submitBtn.disabled = true;
            }
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
    });
</script>
@endsection

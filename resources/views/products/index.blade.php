@extends('layouts.app')

@section('header-title', 'Analisis Produk')
@section('header-description', 'Analisis performa penjualan setiap produk berdasarkan data historis.')

@section('header-actions')
    <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-lg bg-slate-50 text-slate-600 text-xs font-medium border border-slate-200/80">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
        <span>{{ number_format($productCount, 0, ',', '.') }} Produk • {{ $dateRangeFormatted }}</span>
    </div>
@endsection

@section('content')
<div class="space-y-7">

    @if(!$hasData)
        {{-- Empty State --}}
        <div class="bg-white border border-slate-200/80 rounded-xl p-16 text-center">
            <div class="mx-auto w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 mb-4">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-slate-900">
                {{ $productCount === 0 ? 'Belum ada data produk.' : 'Belum ada data penjualan untuk dianalisis.' }}
            </h3>
            <p class="text-[13px] text-slate-500 mt-1 max-w-sm mx-auto leading-relaxed">
                Data historis produk belum tersedia atau belum memiliki catatan penjualan untuk dianalisis.
            </p>
        </div>
    @else
        {{-- 4 Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Card 1: Produk Terlaris --}}
            <div class="bg-white border border-slate-200/80 rounded-xl p-5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Produk Terlaris</span>
                    <span class="p-1.5 bg-emerald-50 text-emerald-500 rounded-lg">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871a2.25 2.25 0 0 0-2.25 2.25V18.75m-6 0V16.5a2.25 2.25 0 0 0-2.25-2.25H6a1.125 1.125 0 0 0-1.125 1.125V18.75" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-base font-bold text-slate-900 tracking-tight truncate" title="{{ $bestProduct['name'] ?? '-' }}">
                        {{ $bestProduct['name'] ?? '-' }}
                    </div>
                    <div class="mt-1 flex items-baseline space-x-1.5">
                        <span class="text-xl font-bold text-slate-900 tabular-nums">
                            {{ isset($bestProduct) ? number_format($bestProduct['total_units'], 0, ',', '.') : '0' }}
                        </span>
                        <span class="text-[11px] text-slate-400">unit</span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5 tabular-nums">
                        {{ isset($bestProduct) ? number_format($bestProduct['average_monthly_units'], 1, ',', '.') : '0' }} unit/bulan
                    </p>
                </div>
            </div>

            {{-- Card 2: Penjualan Terendah --}}
            <div class="bg-white border border-slate-200/80 rounded-xl p-5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Penjualan Terendah</span>
                    <span class="p-1.5 bg-amber-50 text-amber-500 rounded-lg">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-base font-bold text-slate-900 tracking-tight truncate" title="{{ $lowestProduct['name'] ?? '-' }}">
                        {{ $lowestProduct['name'] ?? '-' }}
                    </div>
                    <div class="mt-1 flex items-baseline space-x-1.5">
                        <span class="text-xl font-bold text-slate-900 tabular-nums">
                            {{ isset($lowestProduct) ? number_format($lowestProduct['total_units'], 0, ',', '.') : '0' }}
                        </span>
                        <span class="text-[11px] text-slate-400">unit</span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5 tabular-nums">
                        {{ isset($lowestProduct) ? number_format($lowestProduct['average_monthly_units'], 1, ',', '.') : '0' }} unit/bulan
                    </p>
                </div>
            </div>

            {{-- Card 3: Produk Meningkat --}}
            <div class="bg-white border border-slate-200/80 rounded-xl p-5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Produk Meningkat</span>
                    <span class="p-1.5 bg-emerald-50 text-emerald-500 rounded-lg">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-base font-bold text-slate-900 tracking-tight truncate" title="{{ $topGrowingProduct['name'] ?? 'Tidak ada' }}">
                        {{ $topGrowingProduct['name'] ?? 'Tidak ada' }}
                    </div>
                    <div class="mt-1 flex items-baseline space-x-1.5">
                        <span class="text-xl font-bold text-emerald-600 tabular-nums">
                            {{ isset($topGrowingProduct) ? '+' . number_format($topGrowingProduct['trend']['change_percentage'], 1, ',', '.') . '%' : '-' }}
                        </span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60">Meningkat</span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Pertumbuhan paruh waktu terkuat</p>
                </div>
            </div>

            {{-- Card 4: Produk Menurun --}}
            <div class="bg-white border border-slate-200/80 rounded-xl p-5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Produk Menurun</span>
                    <span class="p-1.5 bg-rose-50 text-rose-500 rounded-lg">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6 9 12.75l4.286-4.286a11.948 11.948 0 0 1 4.306 6.43l.776 2.898m0 0 3.182-5.511m-3.182 5.51-6.19-1.658" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-base font-bold text-slate-900 tracking-tight truncate" title="{{ $topDecliningProduct['name'] ?? 'Tidak ada' }}">
                        {{ $topDecliningProduct['name'] ?? 'Tidak ada' }}
                    </div>
                    <div class="mt-1 flex items-baseline space-x-1.5">
                        <span class="text-xl font-bold text-rose-600 tabular-nums">
                            {{ isset($topDecliningProduct) ? number_format($topDecliningProduct['trend']['change_percentage'], 1, ',', '.') . '%' : '-' }}
                        </span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-rose-50 text-rose-700 border border-rose-200/60">Menurun</span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Penurunan paruh waktu terbesar</p>
                </div>
            </div>
        </div>

        {{-- Product Comparison Bar Chart --}}
        <div class="bg-white border border-slate-200/80 rounded-xl p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 mb-5">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 tracking-tight">Perbandingan Produk</h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">Peringkat volume penjualan seluruh produk secara komparatif.</p>
                </div>
                <span class="text-[11px] text-slate-400">Diurutkan dari volume tertinggi</span>
            </div>
            <div class="relative h-72 sm:h-80 w-full">
                <canvas id="productsComparisonChart"></canvas>
            </div>
        </div>

        {{-- Product Performance Table --}}
        <div class="bg-white border border-slate-200/80 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 tracking-tight">Performa Produk</h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">Rincian performa setiap produk diurutkan berdasarkan total unit terjual.</p>
                </div>
                <span class="text-[11px] text-slate-400">
                    Total: <strong class="text-slate-700 font-semibold">{{ $products->count() }}</strong> produk
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/60 border-b border-slate-200/60 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                            <th scope="col" class="py-3 px-5">Produk</th>
                            <th scope="col" class="py-3 px-5 text-right">Total Unit</th>
                            <th scope="col" class="py-3 px-5 text-right">Rata-rata/Bulan</th>
                            <th scope="col" class="py-3 px-5 text-center">Tren</th>
                            <th scope="col" class="py-3 px-5 text-right">Perubahan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($products as $product)
                            <tr class="hover:bg-slate-50/60 transition-colors duration-100">
                                {{-- Product Name --}}
                                <td class="py-3 px-5">
                                    <div class="font-semibold text-slate-800 text-[13px]">
                                        {{ $product['name'] }}
                                    </div>
                                    @if(!empty($product['category']))
                                        <div class="text-[11px] text-slate-400 mt-0.5">
                                            {{ $product['category'] }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Total Units --}}
                                <td class="py-3 px-5 text-right font-bold text-slate-800 whitespace-nowrap tabular-nums text-[13px]">
                                    {{ number_format($product['total_units'], 0, ',', '.') }}
                                    <span class="text-[11px] text-slate-400 font-normal ml-0.5">unit</span>
                                </td>

                                {{-- Average/Month --}}
                                <td class="py-3 px-5 text-right text-slate-600 whitespace-nowrap tabular-nums text-[13px]">
                                    {{ number_format($product['average_monthly_units'], 1, ',', '.') }}
                                    <span class="text-[11px] text-slate-400">/bln</span>
                                </td>

                                {{-- Trend Status --}}
                                <td class="py-3 px-5 text-center whitespace-nowrap">
                                    @php
                                        $status = $product['trend']['status'];
                                        $statusLabel = $product['trend']['status_label'] ?? match($status) {
                                            'Growing' => 'Meningkat',
                                            'Declining' => 'Menurun',
                                            default => 'Stabil'
                                        };
                                    @endphp

                                    @if($status === 'Growing')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                            </svg>
                                            {{ $statusLabel }}
                                        </span>
                                    @elseif($status === 'Declining')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-rose-50 text-rose-700 border border-rose-200/60">
                                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                            </svg>
                                            {{ $statusLabel }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-50 text-slate-600 border border-slate-200/60">
                                            <svg class="w-3 h-3 mr-1 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                            </svg>
                                            {{ $statusLabel }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Change Percentage --}}
                                <td class="py-3 px-5 text-right whitespace-nowrap tabular-nums text-[12px]">
                                    @php
                                        $change = $product['trend']['change_percentage'] ?? 0.0;
                                    @endphp
                                    @if($change > 0)
                                        <span class="text-emerald-700 font-semibold">+{{ number_format($change, 1, ',', '.') }}%</span>
                                    @elseif($change < 0)
                                        <span class="text-rose-700 font-semibold">{{ number_format($change, 1, ',', '.') }}%</span>
                                    @else
                                        <span class="text-slate-400">0,0%</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

{{-- Chart.js Initialization --}}
@if($hasData)
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof window.Chart === 'undefined') return;

        const chartPayload = @json($chartData);
        const formatNumberId = (val) => new Intl.NumberFormat('id-ID').format(val);

        const tooltipStyle = {
            backgroundColor: '#0F172A',
            titleColor: '#F8FAFC',
            bodyColor: '#F8FAFC',
            padding: { top: 8, bottom: 8, left: 12, right: 12 },
            cornerRadius: 6,
            displayColors: false,
        };

        const canvas = document.getElementById('productsComparisonChart');
        if (canvas) {
            new window.Chart(canvas, {
                type: 'bar',
                data: {
                    labels: chartPayload.labels,
                    datasets: [{
                        label: 'Total Unit Terjual',
                        data: chartPayload.units,
                        backgroundColor: '#4F46E5',
                        borderRadius: 4,
                        barThickness: 16,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            ...tooltipStyle,
                            callbacks: {
                                label: (ctx) => `Total: ${formatNumberId(ctx.parsed.x)} unit`
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            border: { display: false },
                            grid: { color: '#F1F5F9' },
                            ticks: { font: { size: 11 }, callback: (v) => formatNumberId(v) }
                        },
                        y: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: {
                                font: { size: 11 },
                                callback: function(value) {
                                    const label = this.getLabelForValue(value);
                                    return label.length > 22 ? label.substr(0, 20) + '…' : label;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endif
@endsection

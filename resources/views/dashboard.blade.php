@extends('layouts.app')

@section('header-title', 'Dasbor')
@section('header-description', 'Ringkasan dan analisis data penjualan historis.')

@section('header-actions')
    <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-lg bg-slate-50 text-slate-600 text-xs font-medium border border-slate-200/80">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
        <span>{{ $dateRangeFormatted }}</span>
    </div>
@endsection

@section('content')
<div class="space-y-7">

    {{-- Data Status Bar --}}
    <div class="surface-card px-4 py-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-[13px] text-slate-500">
        <span class="font-medium text-slate-700">Status Data Penjualan</span>
        <span class="hidden sm:inline text-slate-300">|</span>
        <span>Produk: <strong class="text-slate-800 font-semibold">{{ number_format($productCount, 0, ',', '.') }}</strong></span>
        <span>Data Penjualan: <strong class="text-slate-800 font-semibold">{{ number_format($salesRecordCount, 0, ',', '.') }} catatan</strong></span>
        <span>Rentang Waktu: <strong class="text-slate-800 font-semibold">{{ $dateRangeFormatted }}</strong></span>
    </div>

    @if($salesRecordCount === 0)
        {{-- Empty State --}}
        <div class="bg-white border border-slate-200/80 rounded-xl p-16 text-center">
            <div class="mx-auto w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 mb-4">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5" />
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-slate-900">Belum ada data penjualan.</h3>
            <p class="text-[13px] text-slate-500 mt-1 max-w-sm mx-auto leading-relaxed">
                Data historis penjualan belum tersedia dalam basis data. Silakan muat data penjualan untuk melihat visualisasi analitis.
            </p>
        </div>
    @else
        {{-- 4 KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- KPI 1: Total Unit Terjual --}}
            <div class="bg-white border border-slate-200/80 rounded-xl p-5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Unit Terjual</span>
                    <span class="p-1.5 bg-indigo-50 text-indigo-500 rounded-lg">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold text-slate-900 tracking-tight tabular-nums">
                        {{ number_format($totalUnits, 0, ',', '.') }}
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Dari seluruh data historis</p>
                </div>
            </div>

            {{-- KPI 2: Rata-rata Unit per Bulan --}}
            <div class="bg-white border border-slate-200/80 rounded-xl p-5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Rata-rata Unit per Bulan</span>
                    <span class="p-1.5 bg-indigo-50 text-indigo-500 rounded-lg">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold text-slate-900 tracking-tight tabular-nums">
                        {{ number_format($averageMonthlyUnits, 1, ',', '.') }}
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Per bulan aktif</p>
                </div>
            </div>

            {{-- KPI 3: Produk Terlaris --}}
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
                    <p class="text-[11px] text-slate-400 mt-1 tabular-nums">
                        {{ isset($bestProduct) ? number_format($bestProduct['total_units'], 0, ',', '.') . ' unit terjual' : '-' }}
                    </p>
                </div>
            </div>

            {{-- KPI 4: Bulan Penjualan Tertinggi --}}
            <div class="bg-white border border-slate-200/80 rounded-xl p-5">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Bulan Penjualan Tertinggi</span>
                    <span class="p-1.5 bg-indigo-50 text-indigo-500 rounded-lg">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-lg font-bold text-indigo-600 tracking-tight">
                        {{ $bestMonth['label_id'] ?? ($bestMonth['label'] ?? '-') }}
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1 tabular-nums">
                        {{ isset($bestMonth) ? number_format($bestMonth['units'], 0, ',', '.') . ' unit terjual' : '-' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Insight Section --}}
        <div class="bg-white border border-slate-200/80 rounded-xl p-5">
            <div class="flex items-center space-x-2.5 mb-3">
                <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.06 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.516 0c.85.493 1.508 1.333 1.508 2.316V18" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900 tracking-tight">Insight Penjualan</h2>
                    <p class="text-[11px] text-slate-400">Ringkasan analitis berbasis data aktual.</p>
                </div>
            </div>
            <ul class="space-y-2 text-[13px] text-slate-600 leading-relaxed">
                @foreach($insights as $insight)
                    <li class="flex items-start space-x-2.5">
                        <span class="w-1 h-1 rounded-full bg-indigo-400 mt-[7px] shrink-0"></span>
                        <span>{!! $insight !!}</span>
                    </li>
                @endforeach
            </ul>

            {{-- Bulan Penjualan Terendah inline --}}
            @if(!empty($lowestMonth))
                <div class="mt-3 pt-3 border-t border-slate-100 text-[13px] text-slate-600">
                    <span class="text-slate-400">Bulan Penjualan Terendah:</span>
                    <strong class="text-slate-800 ml-1">{{ $lowestMonth['label_id'] ?? ($lowestMonth['label'] ?? '-') }}</strong>
                    <span class="text-slate-400 ml-1">({{ isset($lowestMonth) ? number_format($lowestMonth['units'], 0, ',', '.') . ' unit' : '-' }})</span>
                </div>
            @endif
        </div>

        {{-- Main Line Chart: Tren Penjualan Bulanan --}}
        <div class="bg-white border border-slate-200/80 rounded-xl p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-5">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 tracking-tight">Tren Penjualan Bulanan</h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">Volume unit terjual per bulan secara kronologis.</p>
                </div>
                <div class="flex items-center space-x-3 text-[11px] text-slate-400">
                    <span class="inline-flex items-center">
                        <span class="w-2.5 h-[2px] bg-indigo-600 mr-1.5 rounded-full"></span>
                        Unit Terjual
                    </span>
                    <span>Rata-rata: {{ number_format($averageMonthlyUnits, 1, ',', '.') }} unit/bln</span>
                </div>
            </div>
            <div class="relative h-64 sm:h-72 w-full">
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>

        {{-- Product Bar Charts (Side by Side) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Top 5 --}}
            <div class="bg-white border border-slate-200/80 rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 tracking-tight">Produk Terlaris</h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">5 produk dengan volume penjualan tertinggi.</p>
                    </div>
                    <span class="text-[11px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60">Top 5</span>
                </div>
                <div class="relative h-56 sm:h-64 w-full">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>

            {{-- Bottom 5 --}}
            <div class="bg-white border border-slate-200/80 rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 tracking-tight">Produk dengan Penjualan Terendah</h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">5 produk dengan volume penjualan terendah.</p>
                    </div>
                    <span class="text-[11px] font-medium text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200/60">Bottom 5</span>
                </div>
                <div class="relative h-56 sm:h-64 w-full">
                    <canvas id="bottomProductsChart"></canvas>
                </div>
            </div>
        </div>
    @endif

</div>

{{-- Chart.js Initialization --}}
@if($salesRecordCount > 0)
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof window.Chart === 'undefined') return;

        const chartPayload = @json($chartData);

        window.Chart.defaults.font.family = 'Inter, ui-sans-serif, system-ui, sans-serif';
        window.Chart.defaults.color = '#94A3B8';

        const formatNumberId = (val) => new Intl.NumberFormat('id-ID').format(val);

        // Shared tooltip style
        const tooltipStyle = {
            backgroundColor: '#0F172A',
            titleColor: '#F8FAFC',
            bodyColor: '#F8FAFC',
            padding: { top: 8, bottom: 8, left: 12, right: 12 },
            cornerRadius: 6,
            titleFont: { size: 12, weight: '600' },
            bodyFont: { size: 12 },
            displayColors: false,
        };

        // 1. Monthly Trend Line Chart
        const monthlyCanvas = document.getElementById('monthlyTrendChart');
        if (monthlyCanvas) {
            new window.Chart(monthlyCanvas, {
                type: 'line',
                data: {
                    labels: chartPayload.monthly.labels,
                    datasets: [{
                        label: 'Unit Terjual',
                        data: chartPayload.monthly.units,
                        borderColor: '#4F46E5',
                        backgroundColor: 'rgba(79, 70, 229, 0.06)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2,
                        pointRadius: 3,
                        pointBackgroundColor: '#4F46E5',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 1.5,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: '#4338CA',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            ...tooltipStyle,
                            callbacks: {
                                label: (ctx) => `Unit Terjual: ${formatNumberId(ctx.parsed.y)} unit`
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: { maxRotation: 45, font: { size: 11 } }
                        },
                        y: {
                            beginAtZero: true,
                            border: { display: false },
                            grid: { color: '#F1F5F9' },
                            ticks: { font: { size: 11 }, callback: (v) => formatNumberId(v) }
                        }
                    }
                }
            });
        }

        // Shared bar chart options factory
        const barOptions = (isAmber) => ({
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
        });

        // 2. Top Products
        const topCanvas = document.getElementById('topProductsChart');
        if (topCanvas) {
            new window.Chart(topCanvas, {
                type: 'bar',
                data: {
                    labels: chartPayload.topProducts.labels,
                    datasets: [{
                        label: 'Unit Terjual',
                        data: chartPayload.topProducts.units,
                        backgroundColor: '#4F46E5',
                        borderRadius: 4,
                        barThickness: 16,
                    }]
                },
                options: barOptions(false)
            });
        }

        // 3. Bottom Products
        const bottomCanvas = document.getElementById('bottomProductsChart');
        if (bottomCanvas) {
            new window.Chart(bottomCanvas, {
                type: 'bar',
                data: {
                    labels: chartPayload.bottomProducts.labels,
                    datasets: [{
                        label: 'Unit Terjual',
                        data: chartPayload.bottomProducts.units,
                        backgroundColor: '#D97706',
                        borderRadius: 4,
                        barThickness: 16,
                    }]
                },
                options: barOptions(true)
            });
        }
    });
</script>
@endif
@endsection

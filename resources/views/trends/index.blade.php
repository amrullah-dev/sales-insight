@extends('layouts.app')

@section('header-title', 'Analisis Tren Penjualan')
@section('header-description', 'Melihat perkembangan penjualan historis dari waktu ke waktu.')

@section('header-actions')
    @if(!empty($monthlyTrend))
        <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 text-xs font-medium border border-slate-200">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>{{ count($monthlyTrend) }} Bulan • {{ $dateRange }}</span>
        </div>
    @endif
@endsection

@section('content')
<div class="space-y-7">

    @if(empty($monthlyTrend))
        {{-- Keadaan Kosong (Empty State) --}}
        <div class="bg-white border border-slate-200 rounded-xl p-12 text-center shadow-xs">
            <div class="mx-auto w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 mb-4">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                </svg>
            </div>
            <h2 class="text-base font-semibold text-slate-900">Belum ada data penjualan.</h2>
            <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                Analisis tren historis belum dapat ditampilkan sampai data penjualan tersedia.
            </p>
        </div>
    @else
        {{-- 4 Kartu Ringkasan Tren --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            {{-- Kartu 1: Rata-rata Unit per Bulan --}}
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Rata-rata Unit per Bulan</span>
                    <span class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-bold text-slate-900 tracking-tight">
                        {{ number_format($averageMonthlyUnits, 1, ',', '.') }}
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Unit rata-rata per bulan aktif</p>
                </div>
            </div>

            {{-- Kartu 2: Penjualan Tertinggi --}}
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Penjualan Tertinggi</span>
                    <span class="p-1.5 bg-emerald-50 text-emerald-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold text-indigo-600 tracking-tight">
                        {{ $highestMonth['label_id'] ?? 'Belum tersedia' }}
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        {{ isset($highestMonth['units']) ? number_format($highestMonth['units'], 0, ',', '.') . ' unit terjual' : 'Belum tersedia' }}
                    </p>
                </div>
            </div>

            {{-- Kartu 3: Penjualan Terendah --}}
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Penjualan Terendah</span>
                    <span class="p-1.5 bg-amber-50 text-amber-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold text-slate-900 tracking-tight">
                        {{ $lowestMonth['label_id'] ?? 'Belum tersedia' }}
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        {{ isset($lowestMonth['units']) ? number_format($lowestMonth['units'], 0, ',', '.') . ' unit terjual' : 'Belum tersedia' }}
                    </p>
                </div>
            </div>

            {{-- Kartu 4: Perubahan Bulanan Terakhir --}}
            <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Perubahan Terakhir</span>
                    <span class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3">
                    @if($latestChange !== null && ($latestMonth['previous_units'] ?? null) !== 0)
                        <div class="text-3xl font-bold {{ $latestStatus['class'] }} tracking-tight">
                            {{ $latestChange > 0 ? '+' : '' }}{{ number_format($latestChange, 1, ',', '.') }}%
                        </div>
                    @else
                        <div class="text-3xl font-bold text-slate-400 tracking-tight">
                            {{ ($latestMonth['previous_units'] ?? null) === 0 ? 'N/A' : '—' }}
                        </div>
                    @endif
                    <p class="text-xs text-slate-500 mt-1">
                        @if($latestChange !== null && ($latestMonth['previous_units'] ?? null) !== 0)
                            Dibandingkan bulan sebelumnya
                        @elseif(($latestMonth['previous_units'] ?? null) === 0)
                            Tidak tersedia (bulan sebelumnya 0 unit)
                        @else
                            Belum tersedia
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Insight Penjualan (Ringkasan Naratif Dinamis) --}}
        <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-xs">
            <div class="flex items-center space-x-2.5 mb-3">
                <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.516 0c.85.493 1.508 1.333 1.508 2.316V18" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900 tracking-tight">Ringkasan Tren</h2>
                    <p class="text-xs text-slate-500">Interpretasi otomatis berdasarkan data aktual.</p>
                </div>
            </div>
            <ul class="space-y-2 text-sm text-slate-700">
                <li class="flex items-start space-x-2.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 mt-2 shrink-0"></span>
                    <span class="leading-relaxed">
                        @if($latestChange === null)
                            Data baru memiliki satu periode, sehingga perubahan terhadap bulan sebelumnya belum tersedia.
                        @elseif(($latestMonth['previous_units'] ?? null) === 0)
                            Perubahan {{ $latestMonth['label_id'] }} tidak tersedia karena bulan sebelumnya memiliki 0 unit.
                        @elseif($latestStatus['label'] === 'Meningkat')
                            Penjualan pada <strong>{{ $latestMonth['label_id'] }}</strong> meningkat <strong>{{ number_format(abs($latestChange), 1, ',', '.') }}%</strong> dibandingkan bulan sebelumnya.
                        @elseif($latestStatus['label'] === 'Menurun')
                            Penjualan pada <strong>{{ $latestMonth['label_id'] }}</strong> menurun <strong>{{ number_format(abs($latestChange), 1, ',', '.') }}%</strong> dibandingkan bulan sebelumnya.
                        @else
                            Penjualan pada <strong>{{ $latestMonth['label_id'] }}</strong> relatif stabil dibandingkan bulan sebelumnya.
                        @endif
                    </span>
                </li>
                @if($highestMonth && $lowestMonth)
                    <li class="flex items-start space-x-2.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 mt-2 shrink-0"></span>
                        <span class="leading-relaxed">
                            Bulan tertinggi adalah <strong>{{ $highestMonth['label_id'] }}</strong> ({{ number_format($highestMonth['units'], 0, ',', '.') }} unit), sedangkan terendah adalah <strong>{{ $lowestMonth['label_id'] }}</strong> ({{ number_format($lowestMonth['units'], 0, ',', '.') }} unit).
                        </span>
                    </li>
                    @php
                        $spread = $highestMonth['units'] - $lowestMonth['units'];
                        $spreadPct = $lowestMonth['units'] > 0 ? round(($spread / $lowestMonth['units']) * 100, 1) : 0;
                    @endphp
                    @if($spreadPct > 0)
                        <li class="flex items-start space-x-2.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 mt-2 shrink-0"></span>
                            <span class="leading-relaxed">
                                Selisih antara bulan tertinggi dan terendah adalah <strong>{{ number_format($spread, 0, ',', '.') }} unit</strong> ({{ number_format($spreadPct, 1, ',', '.') }}%).
                            </span>
                        </li>
                    @endif
                @endif
            </ul>
        </div>

        {{-- Grafik Utama: Tren Penjualan Bulanan (Line Chart) --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Tren Penjualan Bulanan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pergerakan total unit terjual berdasarkan bulan kalender.</p>
                </div>
                <div class="flex items-center space-x-3 text-xs text-slate-500">
                    <span class="inline-flex items-center">
                        <span class="w-3 h-0.5 bg-indigo-600 mr-1.5"></span>
                        Unit Terjual
                    </span>
                    <span class="inline-flex items-center text-slate-400">
                        Rata-rata: {{ number_format($averageMonthlyUnits, 1, ',', '.') }} unit/bln
                    </span>
                </div>
            </div>
            <div class="relative h-72 sm:h-80 w-full">
                <canvas id="monthlySalesTrendChart" aria-label="Grafik tren penjualan bulanan"></canvas>
            </div>
        </div>

        {{-- Tabel Rincian Tren Bulanan --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Rincian Tren Bulanan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Perubahan penjualan bulan ke bulan secara kronologis.</p>
                </div>
                <div class="text-xs text-slate-500">
                    Total: <strong class="text-slate-900 font-semibold">{{ count($monthlyTrend) }}</strong> periode
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/75 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                            <th scope="col" class="py-3 px-6">Bulan</th>
                            <th scope="col" class="py-3 px-6 text-right">Total Unit</th>
                            <th scope="col" class="py-3 px-6 text-right">Perubahan dari Bulan Sebelumnya</th>
                            <th scope="col" class="py-3 px-6 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($monthlyTrend as $month)
                            @php
                                $hasPrevious = $month['change_percentage'] !== null;
                                $previousIsZero = ($month['previous_units'] ?? null) === 0;
                                $status = $month['status'];
                            @endphp
                            <tr class="hover:bg-slate-50/75 transition-colors">
                                {{-- Kolom Bulan --}}
                                <td class="py-3.5 px-6 font-semibold text-slate-900 whitespace-nowrap">
                                    {{ $month['label_id'] }}
                                </td>

                                {{-- Kolom Total Unit --}}
                                <td class="py-3.5 px-6 text-right font-bold text-slate-900 whitespace-nowrap">
                                    {{ number_format($month['units'], 0, ',', '.') }}
                                    <span class="text-xs text-slate-400 font-normal">unit</span>
                                </td>

                                {{-- Kolom Perubahan MoM --}}
                                <td class="py-3.5 px-6 text-right whitespace-nowrap font-mono text-xs">
                                    @if(! $hasPrevious)
                                        <span class="text-slate-400">—</span>
                                    @elseif($previousIsZero)
                                        <span class="text-slate-500">Tidak tersedia</span>
                                    @else
                                        @if($month['change_percentage'] > 0)
                                            <span class="text-emerald-700 font-semibold">+{{ number_format($month['change_percentage'], 1, ',', '.') }}%</span>
                                        @elseif($month['change_percentage'] < 0)
                                            <span class="text-rose-700 font-semibold">{{ number_format($month['change_percentage'], 1, ',', '.') }}%</span>
                                        @else
                                            <span class="text-slate-500">0,0%</span>
                                        @endif
                                    @endif
                                </td>

                                {{-- Kolom Status (Badge Pill) --}}
                                <td class="py-3.5 px-6 text-center whitespace-nowrap">
                                    @if($status['label'] === 'Meningkat')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <svg class="w-3 h-3 mr-1 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                            </svg>
                                            Meningkat
                                        </span>
                                    @elseif($status['label'] === 'Menurun')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">
                                            <svg class="w-3 h-3 mr-1 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" />
                                            </svg>
                                            Menurun
                                        </span>
                                    @elseif($status['label'] === 'Stabil')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-50 text-slate-700 border border-slate-200">
                                            <svg class="w-3 h-3 mr-1 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                            </svg>
                                            Stabil
                                        </span>
                                    @elseif($status['label'] === 'Tidak tersedia')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-50 text-slate-400 border border-slate-200">
                                            Tidak tersedia
                                        </span>
                                    @else
                                        <span class="text-slate-400">—</span>
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

{{-- Inisialisasi Chart.js untuk Tren Penjualan --}}
@if(!empty($monthlyTrend))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof window.Chart === 'undefined') {
            return;
        }

        const chartPayload = @json($chartData);

        // Pengaturan standar font dan warna Chart.js
        window.Chart.defaults.font.family = 'Inter, ui-sans-serif, system-ui, sans-serif';
        window.Chart.defaults.color = '#64748B';

        // Formatter angka format Indonesia
        const formatNumberId = (val) => new Intl.NumberFormat('id-ID').format(val);

        const canvas = document.getElementById('monthlySalesTrendChart');
        if (!canvas) return;

        new window.Chart(canvas, {
            type: 'line',
            data: {
                labels: chartPayload.labels,
                datasets: [{
                    label: 'Total Unit Terjual',
                    data: chartPayload.units,
                    borderColor: '#4F46E5',
                    backgroundColor: 'rgba(79, 70, 229, 0.08)',
                    fill: true,
                    tension: 0.3,
                    borderWidth: 2.2,
                    pointRadius: 3.5,
                    pointBackgroundColor: '#4F46E5',
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#4338CA',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        titleColor: '#F8FAFC',
                        bodyColor: '#F8FAFC',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: (context) => `Unit Terjual: ${formatNumberId(context.parsed.y)} unit`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 45,
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#F1F5F9'
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            callback: (val) => formatNumberId(val)
                        }
                    }
                }
            }
        });
    });
</script>
@endif
@endsection

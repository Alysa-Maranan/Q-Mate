@extends('layouts.app')

@section('content')
@include('components.sidebar')

<style>
    body {
        background: linear-gradient(135deg, #f5f0eb 0%, #efe8e4 50%, #e8dcd6 100%);
        min-height: 100vh;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    /* Navigation Bar */
    .nav-bar {
        background: linear-gradient(135deg, #fffdfa 0%, #f5eee6 100%);
        border-bottom: 2px solid #d7ccc8;
        box-shadow: 0 4px 20px rgba(121, 85, 72, 0.08);
        padding: 1.25rem 3rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        box-sizing: border-box;
    }

    .logo-section {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .logo {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(121, 85, 72, 0.18);
        border: 2px solid #a1887f;
        overflow: hidden;
    }

    .logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .logo-text {
        color: #6d4c41;
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: 0.5px;
    }

    .nav-menu {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .nav-item {
        color: #6d4c41;
        text-decoration: none;
        font-weight: 700;
        font-size: 1rem;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        background: #fffdfa;
        border: 2px solid #a1887f;
        box-shadow: 0 4px 12px rgba(141, 110, 99, 0.10);
        position: relative;
        overflow: hidden;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
    }

    .nav-item:hover {
        background: #f8ede3;
        box-shadow: 0 6px 16px rgba(141, 110, 99, 0.18);
    }

    .nav-item.active {
        background: #a1887f;
        color: #fffdfa;
        box-shadow: 0 6px 16px rgba(141, 110, 99, 0.18), 0 0 20px rgba(161, 136, 127, 0.5);
    }

    @media (max-width: 768px) {
        .nav-menu { display: none; }
    }

    /* Page Header */
    .page-header {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12);
        border: 1px solid rgba(161, 136, 127, 0.1);
        text-align: center;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 800;
        color: #6d4c41;
        margin: 0;
    }

    .page-subtitle {
        color: #a1887f;
        font-size: 0.875rem;
        margin: 0;
    }

    /* Section Card */
    .section-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12);
        border: 1px solid rgba(161, 136, 127, 0.1);
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #6d4c41;
        margin: 0 0 1.5rem 0;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #efebe9;
    }

    /* Current Reading Cards */
    .reading-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .reading-card {
        background: linear-gradient(135deg, #fffdfa 0%, #f5f0eb 100%);
        border-radius: 16px;
        padding: 1.25rem;
        text-align: center;
        border: 2px solid #efebe9;
        box-shadow: 0 6px 20px rgba(161, 136, 127, 0.1);
        transition: all 0.3s ease;
    }

    .reading-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 30px rgba(161, 136, 127, 0.15);
    }

    .reading-emoji {
        font-size: 2.2rem;
        margin-bottom: 0.5rem;
    }

    .reading-value {
        font-size: 1.75rem;
        font-weight: 800;
        color: #6d4c41;
        margin: 0.3rem 0;
    }

    .reading-label {
        color: #a1887f;
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .reading-status {
        display: inline-block;
        padding: 0.6rem 1.5rem;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 700;
    }

    .status-optimal {
        background: #d1fae5;
        color: #065f46;
    }

    .status-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .status-hot {
        background: #fee2e2;
        color: #991b1b;
    }

        .status-cold {
        background: #dbeafe;
        color: #1e40af;
    }

    /* Form Styles */
    .input-group {
        margin-bottom: 1.25rem;
    }

    .input-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #6d4c41;
        margin-bottom: 0.5rem;
    }

    .input-field {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1.5px solid #d7ccc8;
        border-radius: 10px;
        font-size: 0.95rem;
        background: #f9fafb;
        transition: all 0.3s ease;
    }

    .input-field:focus {
        outline: none;
        border-color: #a1887f;
        background: white;
    }

    .input-field:focus {
        outline: none;
        border-color: #a1887f;
        box-shadow: 0 0 0 3px rgba(161, 136, 127, 0.1);
    }

    /* Button Styles */
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        font-size: 0.95rem;
    }
    .btn-primary {
        background: linear-gradient(135deg, #efebe9 0%, #d7ccc8 100%);
        color: #6d4c41;
        box-shadow: 0 4px 12px rgba(161, 136, 127, 0.3);
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(161, 136, 127, 0.4);
    }
    .btn-danger {
        background: #fee2e2;
        color: #991b1b;
        border: none;
        padding: 0.4rem 0.9rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-danger:hover { background: #fecaca; }
    .status-pill {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }

    /* Charts Container */
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .chart-card {
        background: white;
        border-radius: 16px;
        padding: 1rem;
        box-shadow: 0 4px 12px rgba(161, 136, 127, 0.08);
        border: 1px solid #efebe9;
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .chart-title {
        font-weight: 700;
        color: #6d4c41;
        font-size: 0.9rem;
    }

    /* Chart Type Toggle Buttons */
    .chart-type-toggle {
        display: inline-flex;
        background: #f5f0eb;
        border-radius: 10px;
        padding: 3px;
        gap: 2px;
        box-shadow: inset 0 1px 3px rgba(121, 85, 72, 0.08);
    }

    .chart-type-btn {
        padding: 0.35rem 0.9rem;
        border: none;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #8d6e63;
        background: transparent;
        cursor: pointer;
        transition: all 0.25s ease;
        font-family: inherit;
    }

    .chart-type-btn:hover {
        color: #6d4c41;
        background: rgba(255, 255, 255, 0.6);
    }

    .chart-type-btn.active {
        background: #ffffff;
        color: #6d4c41;
        box-shadow: 0 2px 6px rgba(121, 85, 72, 0.18);
    }

    /* Table Styles */
    .table-responsive {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    table thead {
        background: #efebe9;
        border-bottom: 2px solid #d7ccc8;
    }

    table th {
        padding: 0.9rem 1rem;
        text-align: left;
        color: #8d6e63;
        font-weight: 600;
        font-size: 0.875rem;
    }

    table td {
        padding: 0.9rem 1rem;
        color: #4e342e;
        border-bottom: 1px solid #efebe9;
        vertical-align: middle;
        font-size: 0.875rem;
    }

    table tbody tr:hover {
        background: #faf8f6;
    }

    /* Search Box */
    .search-box {
        width: 100%;
        padding: 0.5rem 1rem;
        border: 2px solid #efebe9;
        border-radius: 8px;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }
    .search-box:focus {
        outline: none;
        border-color: #a1887f;
    }

    /* Sidebar content wrapper - matching Dashboard/Settings */
    .sidebar-content-wrap {
        margin-left: 300px;
        transition: margin-left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        padding-top: 0;
    }
    .sidebar-content-wrap.expanded {
        margin-left: 0;
        padding-top: 80px;
    }

    /* Expanded state - Full width content */
    .sidebar-content-wrap.expanded .container {
        max-width: 1600px;
        margin: 0 auto;
        padding-left: 2rem;
        padding-right: 2rem;
    }

    @media (max-width: 768px) {
        .sidebar-content-wrap {
            margin-left: 0 !important;
            padding-top: 70px;
        }
        .container { padding: 1.5rem 1rem; }
        .nav-menu { display: none; }
    }
</style>

<div id="main-content-wrap" class="sidebar-content-wrap">
<div class="container" style="padding: 2rem;">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Temperature & Humidity</h1>
        <p class="page-subtitle">Real-time Philippine Weather Monitoring &nbsp;|&nbsp; Refreshing in <span id="refresh-countdown" style="font-weight:700;color:#6d4c41;">60</span>s</p>
        <p id="weather-location" style="font-size:0.875rem;color:#8d6e63;margin-top:0.5rem;font-weight:600;">Loading weather data...</p>
        @isset($currentBreeds)
        @if(count($currentBreeds) > 0)
        @php
            // Get unique breeds to avoid showing duplicates
            $uniqueBreeds = [];
            $breedIds = [];
            foreach ($currentBreeds as $breed) {
                if (!in_array($breed->id, $breedIds)) {
                    $uniqueBreeds[] = $breed;
                    $breedIds[] = $breed->id;
                }
            }
        @endphp
        <div style="margin-top: 1rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
            @foreach($uniqueBreeds as $index => $breed)
            <div style="padding: 1rem; background: linear-gradient(135deg, #fff9f5 0%, #fff7f2 100%); border-radius: 12px; border-left: 4px solid #a1887f;">
                <div style="font-weight: 700; color: #6d4c41; font-size: 0.95rem; margin-bottom: 0.75rem;">Breed #{{ $index + 1 }}: {{ $breed->name }}</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.8rem; color: #5d4037;">
                    <div><strong>Temp:</strong> {{ $breed->optimal_temperature ?? 'N/A' }}°C</div>
                    <div><strong>Humidity:</strong> {{ $breed->optimal_humidity ?? 'N/A' }}%</div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
        @endisset
    </div>

    <!-- Current Readings -->
    <div class="section-card">
        <h3 class="section-title">Current Reading</h3>
        <div class="reading-cards">
            <div class="reading-card">
                <div class="reading-emoji">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#795548" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"/></svg>
                </div>
                <div class="reading-label">Temperature</div>
                <div class="reading-value" id="current-temp">{{ $latest ? round($latest->temperature, 1) . '°C' : ($currentQuailBreed ? $currentQuailBreed->optimal_temperature . '°C' : '—°C') }}</div>
                <span class="reading-status {{ $latest ? ($latest->temperature < 25 ? 'status-cold' : ($latest->temperature > 35 ? 'status-hot' : 'status-optimal')) : 'status-warning' }}" id="temp-status">
                    {{ $latest ? ($latest->temperature < 25 ? 'Too Cold' : ($latest->temperature > 35 ? 'Too Hot' : 'Optimal')) : 'Check' }}
                </span>
            </div>
            <div class="reading-card">
                <div class="reading-emoji">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#795548" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>
                </div>
                <div class="reading-label">Humidity</div>
                <div class="reading-value" id="current-humidity">{{ $latest ? round($latest->humidity, 1) . '%' : ($currentQuailBreed ? $currentQuailBreed->optimal_humidity . '%' : '—%') }}</div>
                <span class="reading-status {{ $latest ? ($latest->humidity < 60 ? 'status-cold' : ($latest->humidity > 85 ? 'status-hot' : 'status-optimal')) : 'status-warning' }}" id="humidity-status">
                    {{ $latest ? ($latest->humidity < 60 ? 'Too Dry' : ($latest->humidity > 85 ? 'Too Humidity' : 'Optimal')) : 'Check' }}
                </span>
            </div>
            <div class="reading-card" id="feels-like-card" style="display:none;">
                <div class="reading-emoji">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#795548" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"/></svg>
                </div>
                <div class="reading-label">Feels Like</div>
                <div class="reading-value" id="feels-like">—°C</div>
                <span class="reading-status status-optimal">Weather API</span>
            </div>
        </div>
    </div>

    <!-- 24h Average Stats -->
    <div class="section-card">
        <h3 class="section-title">24-Hour Averages</h3>
        <div class="reading-cards">
            <div class="reading-card">
                <div class="reading-emoji">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#795548" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
                </div>
                <div class="reading-label">Avg Temperature (24h)</div>
                <div class="reading-value" id="avg-temp">{{ $avg24h ? round($avg24h->avg_temperature, 1) . '°C' : '—°C' }}</div>
            </div>
            <div class="reading-card">
                <div class="reading-emoji">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#795548" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
                </div>
                <div class="reading-label">Avg Humidity (24h)</div>
                <div class="reading-value" id="avg-humidity">{{ $avg24h ? round($avg24h->avg_humidity, 1) . '%' : '—%' }}</div>
            </div>
            <div class="reading-card">
                <div class="reading-emoji">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#795548" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
                <div class="reading-label">Total Readings</div>
                <div class="reading-value" id="total-readings">{{ $historyCount ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="section-card">
        <h3 class="section-title">24-Hour Trends</h3>
        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-header">
                    <span class="chart-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#795548" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"/></svg>
                        Temperature Trend</span>
                    <div class="chart-type-toggle">
                        <button type="button" class="chart-type-btn active" data-chart="temp" data-type="line">Line</button>
                        <button type="button" class="chart-type-btn" data-chart="temp" data-type="bar">Bar</button>
                    </div>
                </div>
                <canvas id="tempChart" height="200"></canvas>
            </div>
            <div class="chart-card">
                <div class="chart-header">
                    <span class="chart-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#795548" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px;"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>
                        Humidity Trend</span>
                    <div class="chart-type-toggle">
                        <button type="button" class="chart-type-btn active" data-chart="hum" data-type="line">Line</button>
                        <button type="button" class="chart-type-btn" data-chart="hum" data-type="bar">Bar</button>
                    </div>
                </div>
                <canvas id="humidityChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Readings -->
    <div class="section-card">
        <h3 class="section-title">Recent Readings</h3>
        <div style="margin-bottom:1rem;display:flex;gap:1rem;flex-wrap:wrap;align-items:center;">
            <input type="text" id="searchTable" class="search-box" placeholder="Search readings..." onkeyup="filterTable()" style="flex:1;min-width:200px;">
            <button class="btn btn-primary" onclick="refreshReadings()" title="Refresh data">Refresh</button>
        </div>
        <div style="margin-bottom:1rem;display:flex;gap:0.5rem;">
            <button class="btn btn-primary" onclick="loadHistory('24')">24h</button>
            <button class="btn btn-primary" onclick="loadHistory('168')">Week</button>
            <button class="btn btn-primary" onclick="loadHistory('720')">Month</button>
        </div>
        <div class="table-responsive">
            <table id="readingsTable">
                <thead>
                    <tr>
                        <th style="width:25%;">Date & Time</th>
                        <th style="width:20%;">Temperature</th>
                        <th style="width:20%;">Humidity</th>
                        <th style="width:20%;">Status</th>
                        <th style="width:15%;">Action</th>
                    </tr>
                </thead>
                <tbody id="historyTable">
                    @forelse($history as $reading)
                        <tr>
                            <td>{{ $reading->recorded_at?->format('M d, Y H:i') }}</td>
                            <td><strong>{{ round($reading->temperature, 1) }}°C</strong></td>
                            <td><strong>{{ round($reading->humidity, 1) }}%</strong></td>
                            <td>
                                <span class="reading-status {{ $reading->temperature >= 25 && $reading->temperature <= 35 && $reading->humidity >= 60 && $reading->humidity <= 85 ? 'status-optimal' : 'status-warning' }}">
                                                                        {{ $reading->temperature >= 25 && $reading->temperature <= 35 && $reading->humidity >= 60 && $reading->humidity <= 85 ? 'Optimal' : 'Check' }}
                                </span>
                            </td>
                            <td><button class="btn btn-danger" onclick="deleteReading({{ $reading->id }})" style="font-size:0.8rem;">Delete</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;color:#a1887f;padding:2rem;">No readings yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
class TempHumidityMonitor {
    constructor() {
        this.tempCtx = document.getElementById('tempChart')?.getContext('2d');
        this.humCtx = document.getElementById('humidityChart')?.getContext('2d');
        this.tempChart = null;
        this.humChart = null;
        this.initCharts();
        this.updateReadings();
        this.pollInterval = setInterval(() => this.updateReadings(), 60000); // 1 minute
    }

    initCharts() {
        if (!this.tempCtx || !this.humCtx) return;

        // Shared x-axis time label styling
        const timeTicks = {
            color: '#8d6e63',
            font: { size: 11, family: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif", weight: '500' },
            maxRotation: 0,
            minRotation: 0,
            autoSkip: true,
            maxTicksLimit: 8,
            padding: 8,
        };

        // Shared y-axis styling
        const valueTicks = {
            color: '#a1887f',
            font: { size: 11, family: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif" },
            padding: 6,
        };

        const gridStyle = {
            color: 'rgba(161, 136, 127, 0.12)',
            drawBorder: false,
        };

        // Temperature chart (20-30°C optimal)
        this.tempChart = new Chart(this.tempCtx, {
            type: 'line',
            data: { datasets: [{ label: 'Temp (°C)', borderColor: '#0d6efd', backgroundColor: 'rgba(13,110,253,0.1)', tension: 0.4, pointRadius: 2, pointHoverRadius: 5, pointBackgroundColor: '#0d6efd', borderWidth: 2.5 }] },
            options: {
                responsive: true,
                scales: {
                    x: {
                        grid: { ...gridStyle, display: false },
                        ticks: timeTicks,
                    },
                    y: {
                        min: 20,
                        max: 45,
                        grid: gridStyle,
                        ticks: { ...valueTicks, callback: (v) => v + '°' },
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#5d4037',
                        titleFont: { size: 12, weight: '600' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            title: (items) => this.formatChartTime(items[0].label),
                        }
                    }
                }
            }
        });

        // Humidity chart (50-70% optimal)
        this.humChart = new Chart(this.humCtx, {
            type: 'line',
            data: { datasets: [{ label: 'Humidity (%)', borderColor: '#20c997', backgroundColor: 'rgba(32,201,151,0.1)', tension: 0.4, pointRadius: 2, pointHoverRadius: 5, pointBackgroundColor: '#20c997', borderWidth: 2.5 }] },
            options: {
                responsive: true,
                scales: {
                    x: {
                        grid: { ...gridStyle, display: false },
                        ticks: timeTicks,
                    },
                    y: {
                        min: 40,
                        max: 100,
                        grid: gridStyle,
                        ticks: { ...valueTicks, callback: (v) => v + '%' },
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#5d4037',
                        titleFont: { size: 12, weight: '600' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            title: (items) => this.formatChartTime(items[0].label),
                        }
                    }
                }
            }
        });
    }

    /**
     * Format chart time labels: "2:30 PM" style (clean, readable)
     */
    formatChartTime(value) {
        const d = value instanceof Date ? value : new Date(value);
        if (isNaN(d)) return String(value);
        let hours = d.getHours();
        const minutes = d.getMinutes().toString().padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12;
        return `${hours}:${minutes} ${ampm}`;
    }

    async updateReadings() {
        try {
            let temp, humidity, recordedAt, description, location, feelsLike;
            let dataSource = 'sensor';

            // Try physical sensor first
            const response = await fetch('/api/sensor-data/latest', { cache: 'no-store' });
            const data = await response.json();

            if (data.success && data.temperature != null) {
                temp = parseFloat(data.temperature);
                humidity = parseFloat(data.humidity);
                recordedAt = new Date(data.recorded_at);
                location = 'Sensor reading';
            } else {
                // Fallback: Philippine weather API
                const weatherResp = await fetch('/api/weather/philippines');
                const weatherData = await weatherResp.json();
                if (weatherData.success) {
                    temp = parseFloat(weatherData.temperature);
                    humidity = parseFloat(weatherData.humidity);
                    recordedAt = new Date(weatherData.timestamp);
                    description = weatherData.description;
                    location = weatherData.location;
                    feelsLike = weatherData.feels_like;
                    dataSource = 'weather-api';

                    // Persist weather data to sensor_readings for history
                    try {
                        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        await fetch('/api/sensor-data', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ temperature: temp, humidity: humidity })
                        });
                    } catch (persistErr) {
                        console.warn('Could not persist weather data:', persistErr);
                    }
                } else {
                    const locationEl = document.getElementById('weather-location');
                    if (locationEl) locationEl.textContent = 'Waiting for sensor data...';
                    return;
                }
            }

            document.getElementById('current-temp').textContent = temp.toFixed(1) + '°C';
            document.getElementById('current-humidity').textContent = humidity.toFixed(1) + '%';

            // Display "Feels Like" if available from weather API
            const feelsLikeEl = document.getElementById('feels-like');
            const feelsLikeCard = document.getElementById('feels-like-card');
            if (feelsLikeEl && feelsLikeCard) {
                if (feelsLike) {
                    feelsLikeEl.textContent = feelsLike.toFixed(1) + '°C';
                    feelsLikeCard.style.display = 'block';
                } else {
                    feelsLikeEl.textContent = '—°C';
                    feelsLikeCard.style.display = 'none';
                }
            }

            const locationEl = document.getElementById('weather-location');
            if (locationEl) {
                if (description) {
                    locationEl.textContent = location + ' — ' + description;
                } else {
                    locationEl.textContent = 'Last updated: ' + recordedAt.toLocaleString();
                }
            }

            const s = JSON.parse(localStorage.getItem('tempHumSettings')) || {};
            const minTemp = s.minTemp ?? 25;
            const maxTemp = s.maxTemp ?? 35;
            const minHum  = s.minHum  ?? 60;
            const maxHum  = s.maxHum  ?? 85;

            this.updateStatus('temp-status', temp, minTemp, maxTemp);
            this.updateStatus('humidity-status', humidity, minHum, maxHum);

            // Check alerts and send notifications if enabled
            this.checkAlerts(temp, humidity, minTemp, maxTemp, minHum, maxHum);
        } catch (e) {
            console.warn('Sensor fetch error:', e);
        }
    }

    checkAlerts(temp, humidity, minTemp, maxTemp, minHum, maxHum) {
        const settings = JSON.parse(localStorage.getItem('tempHumSettings')) || {};
        if (settings.alertEnabled === false) return;
        const alertsEnabled = document.getElementById('alertNotifications')?.checked !== false;
        if (!alertsEnabled) return;

        // Cooldown: 5 minutes between same-type notifications
        const now = Date.now();
        const lastNotify = parseInt(localStorage.getItem('lastTempHumNotify') || '0', 10);
        if (now - lastNotify < 300000) return;

        let status = 'normal';
        let statusMsg = '';

        if (temp < minTemp) {
            status = 'warning';
                    statusMsg = `Too cold: ${temp.toFixed(1)}°C (min: ${minTemp}°C)`;
        } else if (temp > maxTemp) {
            status = 'warning';
            statusMsg = `Too hot: ${temp.toFixed(1)}°C (max: ${maxTemp}°C)`;
        }
        if (humidity < minHum) {
            status = status === 'normal' ? 'warning' : status;
            statusMsg += statusMsg ? ' | ' : '';
            statusMsg += `Too dry: ${humidity.toFixed(1)}% (min: ${minHum}%)`;
        } else if (humidity > maxHum) {
            status = status === 'normal' ? 'warning' : status;
            statusMsg += statusMsg ? ' | ' : '';
            statusMsg += `Too Humidity: ${humidity.toFixed(1)}% (max: ${maxHum}%)`;
        }
        if (!statusMsg) {
            statusMsg = `All optimal: ${temp.toFixed(1)}°C / ${humidity.toFixed(1)}%`;
        }

        localStorage.setItem('lastTempHumNotify', String(now));
        try {
            if (typeof notifyTemperatureHumidityStatus === 'function') {
                notifyTemperatureHumidityStatus(status, temp, humidity, statusMsg);
            }
        } catch (e) {
            console.warn('Notification failed:', e);
        }
    }

    // Documented in NOTIFICATION_SYSTEM_GUIDE.md
    checkTemperatureAlerts(temperature) {
        const settings = JSON.parse(localStorage.getItem('tempHumSettings')) || {};
        const minTemp = settings.minTemp ?? 25;
        const maxTemp = settings.maxTemp ?? 35;
        const alertsEnabled = document.getElementById('alertNotifications')?.checked !== false && settings.alertEnabled !== false;
        if (!alertsEnabled) return;
                if (temperature < minTemp) {
            this._sendAlert('warning', temperature, `Temperature: ${temperature.toFixed(1)}°C - Too cold! (min: ${minTemp}°C)`);
        } else if (temperature > maxTemp) {
            this._sendAlert('critical', temperature, `Temperature: ${temperature.toFixed(1)}°C - Too hot! (max: ${maxTemp}°C)`);
        }
    }

    checkHumidityAlerts(humidity) {
        const settings = JSON.parse(localStorage.getItem('tempHumSettings')) || {};
        const minHum = settings.minHum ?? 60;
        const maxHum = settings.maxHum ?? 85;
        const alertsEnabled = document.getElementById('alertNotifications')?.checked !== false && settings.alertEnabled !== false;
        if (!alertsEnabled) return;
        if (humidity < minHum) {
            this._sendAlert('warning', null, `Humidity: ${humidity.toFixed(1)}% - Too dry! (min: ${minHum}%)`);
        } else if (humidity > maxHum) {
            this._sendAlert('critical', null, `Humidity: ${humidity.toFixed(1)}% - Too Humidity! (max: ${maxHum}%)`);
        }
    }

    _sendAlert(type, temp, message) {
        const now = Date.now();
        const lastNotify = parseInt(localStorage.getItem('lastTempHumNotify') || '0', 10);
        if (now - lastNotify < 300000) return;
        localStorage.setItem('lastTempHumNotify', String(now));
        try {
            if (typeof notifyTemperatureHumidityStatus === 'function') {
                const humEl = document.getElementById('current-humidity');
                const hum = humEl ? parseFloat(humEl.textContent) : null;
                notifyTemperatureHumidityStatus(type === 'critical' ? 'critical' : 'warning', temp, hum, message);
            }
        } catch (e) {
            console.warn('Notification failed:', e);
        }
    }

    updateStatus(elementId, value, min, max) {
        const el = document.getElementById(elementId);
        if (!el || value === null) return;

        let label, className;
        if (elementId === 'temp-status') {
            if (value < min) { label = 'Too Cold'; className = 'reading-status status-cold'; }
            else if (value > max) { label = 'Too Hot'; className = 'reading-status status-hot'; }
            else { label = 'Optimal'; className = 'reading-status status-optimal'; }
        } else {
            if (value < min) { label = 'Too Dry'; className = 'reading-status status-cold'; }
            else if (value > max) { label = 'Too Humidity'; className = 'reading-status status-hot'; }
            else { label = 'Optimal'; className = 'reading-status status-optimal'; }
        }
        el.className = className;
        el.textContent = label;
    }

    async loadHistory(hours) {
        try {
            const response = await fetch(`/api/sensor-data/history?hours=${hours}`);
            const data = await response.json();
            
            if (data.success && data.data.length) {
                const settings = JSON.parse(localStorage.getItem('tempHumSettings')) || { minTemp: 25, maxTemp: 35, minHum: 60, maxHum: 85 };
                const tbody = document.getElementById('historyTable');
                tbody.innerHTML = data.data.map(reading => `
                    <tr>
                        <td>${new Date(reading.recorded_at).toLocaleString()}</td>
                        <td><strong>${reading.temperature?.toFixed(1)}°C</strong></td>
                        <td><strong>${reading.humidity?.toFixed(1)}%</strong></td>
                        <td>
                            <span class="reading-status ${reading.temperature >= 25 && reading.temperature <= 35 && reading.humidity >= 60 && reading.humidity <= 85 ? 'status-optimal' : 'status-warning'}">
                                                                ${reading.temperature >= 25 && reading.temperature <= 35 && reading.humidity >= 60 && reading.humidity <= 85 ? 'Optimal' : 'Check'}
                            </span>
                        </td>
                        <td><button class="btn btn-danger" onclick="monitor.deleteReading(${reading.id})" style="font-size:0.8rem;">Delete</button></td>
                    </tr>
                `).join('');
                

                // Update charts
                if (this.tempChart && data.data.length) {
                    this.tempChart.data.labels = data.data.map(r => this.formatChartTime(r.recorded_at));
                    this.tempChart.data.datasets[0].data = data.data.map(r => r.temperature);
                    this.tempChart.update('none');
                    
                    this.humChart.data.labels = this.tempChart.data.labels;
                    this.humChart.data.datasets[0].data = data.data.map(r => r.humidity);
                    this.humChart.update('none');
                }
            }
        } catch (e) {
            console.error('History load error:', e);
        }
    }

    deleteReading(id) {
        if (confirm('Delete this reading?')) {
            fetch(`/sensor-readings/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        // Remove row from table
                        const row = document.querySelector(`tr [onclick="monitor.deleteReading(${id})"]`)?.closest('tr');
                        if (row) row.remove();
                    } else {
                        alert('Error: ' + (result.message || 'Could not delete reading.'));
                    }
                })
                .catch(err => {
                    console.error('Delete error:', err);
                    alert('Could not delete reading. Please try again.');
                });
        }
    }
}

// Initialize
const monitor = new TempHumidityMonitor();

document.addEventListener('DOMContentLoaded', function() {
    loadSettings();
    monitor.loadHistory('24');

    // Countdown timer (resets every minute, matches sensor poll interval)
    let countdown = 60;
    const countdownEl = document.getElementById('refresh-countdown');
    setInterval(() => {
        countdown--;
        if (countdownEl) countdownEl.textContent = countdown;
        if (countdown <= 0) countdown = 60;
    }, 1000);

    // Notify dashboard on initial load (after readings populate)
    setTimeout(notifyCurrentTemperatureHumidityStatus, 500);
});

function changeChartType(chartId, chartType) {
    const chart = chartId === 'temp' ? monitor.tempChart : monitor.humChart;
    if (!chart) return;
    chart.config.type = chartType;
    if (chartType === 'bar') {
        chart.data.datasets.forEach(ds => {
            ds.fill = false;
            ds.borderWidth = 1.5;
            ds.borderRadius = 5;
            ds.borderSkipped = false;
            ds.pointRadius = 0;
        });
    } else {
        chart.data.datasets.forEach(ds => {
            ds.fill = true;
            ds.borderWidth = 2.5;
            ds.borderRadius = undefined;
            ds.pointRadius = 2;
        });
    }
    chart.update();
}

// Bind chart type toggle buttons
document.querySelectorAll('.chart-type-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const group = this.closest('.chart-type-toggle');
        group.querySelectorAll('.chart-type-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        changeChartType(this.dataset.chart, this.dataset.type);
    });
});

function filterTable() {
    const input = document.getElementById('searchTable').value.toLowerCase();
    const rows = document.getElementById('readingsTable').getElementsByTagName('tr');
    for (let i = 1; i < rows.length; i++) {
        rows[i].style.display = rows[i].textContent.toLowerCase().includes(input) ? '' : 'none';
    }
}

function refreshReadings() {
    monitor.updateReadings();
    monitor.loadHistory('24');
}

function saveSettings() {
    const minTemp = parseFloat(document.getElementById('minTemp').value);
    const maxTemp = parseFloat(document.getElementById('maxTemp').value);
    const minHum  = parseFloat(document.getElementById('minHum').value);
    const maxHum  = parseFloat(document.getElementById('maxHum').value);
    if (isNaN(minTemp)||isNaN(maxTemp)||isNaN(minHum)||isNaN(maxHum)) { showSettingsFeedback('Please fill in all fields.', false); return; }
    if (minTemp >= maxTemp) { showSettingsFeedback('Min Temp must be less than Max Temp.', false); return; }
    if (minHum >= maxHum)   { showSettingsFeedback('Min Humidity must be less than Max Humidity.', false); return; }
    const settings = { minTemp, maxTemp, minHum, maxHum, alertEnabled: document.getElementById('alertNotifications').checked };
    localStorage.setItem('tempHumSettings', JSON.stringify(settings));
    const t = parseFloat(document.getElementById('current-temp').textContent);
    const h = parseFloat(document.getElementById('current-humidity').textContent);
    if (!isNaN(t)) monitor.updateStatus('temp-status', t, minTemp, maxTemp);
    if (!isNaN(h)) monitor.updateStatus('humidity-status', h, minHum, maxHum);
    showSettingsFeedback('Settings saved successfully.', true);
}

function resetSettings() {
    document.getElementById('minTemp').value = 25;
    document.getElementById('maxTemp').value = 35;
    document.getElementById('minHum').value  = 60;
    document.getElementById('maxHum').value  = 85;
    document.getElementById('alertNotifications').checked = true;
    localStorage.removeItem('tempHumSettings');
    showSettingsFeedback('Settings reset to default.', true);
}

function loadSettings() {
    const s = JSON.parse(localStorage.getItem('tempHumSettings'));
    if (!s) return;
    document.getElementById('minTemp').value = s.minTemp;
    document.getElementById('maxTemp').value = s.maxTemp;
    document.getElementById('minHum').value  = s.minHum;
    document.getElementById('maxHum').value  = s.maxHum;
    document.getElementById('alertNotifications').checked = s.alertEnabled !== false;
}

function showSettingsFeedback(message, success) {
    let el = document.getElementById('settings-feedback');
    if (!el) {
        el = document.createElement('div');
        el.id = 'settings-feedback';
        el.style.cssText = 'margin-top:1rem;padding:0.75rem 1rem;border-radius:8px;font-size:0.9rem;font-weight:600;';
        document.getElementById('alertNotifications').closest('div').after(el);
    }
    el.style.background = success ? '#d1fae5' : '#fee2e2';
    el.style.color = success ? '#065f46' : '#991b1b';
    el.textContent = message;
    el.style.display = 'block';
    setTimeout(() => { el.style.display = 'none'; }, 3000);
}

/**
 * Notify temperature/humidity status to dashboard on page load
 * Checks current readings and alerts status
 */
function notifyCurrentTemperatureHumidityStatus() {
    if (typeof notifyTemperatureHumidityStatus !== 'function') return;
    
    // Get current values from page
    const tempEl = document.getElementById('current-temp');
    const humEl = document.getElementById('current-humidity');
    
    if (!tempEl || !humEl) return;
    
    const temp = parseFloat(tempEl.textContent);
    const humidity = parseFloat(humEl.textContent);
    
    if (isNaN(temp) || isNaN(humidity)) return;
    
    // Get alert thresholds from settings
    const settings = JSON.parse(localStorage.getItem('tempHumSettings') || '{"minTemp":25,"maxTemp":35,"minHum":60,"maxHum":85}');
    const { minTemp, maxTemp, minHum, maxHum } = settings;
    
    // Determine status
    let status = 'normal';
    let statusMsg = '';
    
    if (temp < minTemp || temp > maxTemp) {
        status = temp > maxTemp ? 'critical' : 'warning';
        statusMsg = `Temperature ${temp}°C is ${temp > maxTemp ? 'too high' : 'too low'} (normal: ${minTemp}°C - ${maxTemp}°C)`;
    }
    
    if (humidity < minHum || humidity > maxHum) {
        const humStatus = humidity > maxHum ? 'critical' : 'warning';
        if (status === 'critical' || humStatus === 'critical') {
            status = 'critical';
        } else if (status !== 'critical') {
            status = humStatus;
        }
        const msg = `Humidity ${humidity}% is ${humidity > maxHum ? 'too high' : 'too low'} (normal: ${minHum}% - ${maxHum}%)`;
        statusMsg = statusMsg ? statusMsg + ' | ' + msg : msg;
    }
    
    if (!statusMsg) {
        statusMsg = `Temperature ${temp.toFixed(1)}°C, Humidity ${humidity.toFixed(1)}% - All normal`;
    }
    
    // Notify dashboard
    try {
        notifyTemperatureHumidityStatus(status, temp, humidity, statusMsg);
    } catch(e) {
        // Silent fail if function not available
    }
}

// Page load complete — initial notification handled above in DOMContentLoaded
</script>

@endpush
@endsection

@extends('layouts.app')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #f5f0eb 0%, #efe8e4 50%, #e8dcd6 100%);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .sidebar-content-wrap {
        margin-left: 260px;
        transition: margin-left 0.3s ease;
        padding-top: 0;
    }

    .analytics-container {
        padding: 2rem;
    }

    .analytics-header {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12);
        border: 1px solid rgba(161, 136, 127, 0.1);
    }

    .analytics-header h1 {
        font-size: 2rem;
        font-weight: 800;
        color: #6d4c41;
        margin: 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 16px;
        border: 2px solid #d7ccc8;
        box-shadow: 0 8px 32px rgba(161, 136, 127, 0.08);
        text-align: center;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(161, 136, 127, 0.15);
    }

    .stat-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #8d6e63;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        color: #6d4c41;
        margin: 0.5rem 0;
    }

    .stat-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .charts-section {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12);
    }

    .charts-section h2 {
        color: #6d4c41;
        margin-bottom: 1.5rem;
        font-size: 1.25rem;
        font-weight: 700;
    }

    .chart-group {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .chart-container {
        background: #efebe9;
        border-radius: 12px;
        padding: 1.5rem;
        position: relative;
        height: 300px;
    }

    .breed-item {
        background: white;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-left: 3px solid #a1887f;
    }

    .breed-name {
        font-weight: 600;
        color: #6d4c41;
    }

    .breed-count {
        background: #a1887f;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 700;
    }

    .export-section {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12);
    }

    .export-btn {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, #6d4c41 0%, #a1887f 100%);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        margin-right: 1rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .export-btn:hover {
        box-shadow: 0 6px 20px rgba(109, 76, 65, 0.3);
        transform: translateY(-2px);
    }

    .date-filter {
        background: #efebe9;
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        display: flex;
        gap: 1rem;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
    }

    .filter-group label {
        font-weight: 600;
        color: #6d4c41;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .filter-group input {
        padding: 0.75rem;
        border: 2px solid #d7ccc8;
        border-radius: 8px;
        font-size: 0.95rem;
    }

    .filter-btn {
        padding: 0.75rem 1.5rem;
        background: #a1887f;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .filter-btn:hover {
        background: #8d6e63;
    }

    @media (max-width: 768px) {
        .analytics-container {
            padding: 1rem;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .date-filter {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-group input {
            width: 100%;
        }
    }
</style>

@include('components.sidebar')

<div id="main-content-wrap" class="sidebar-content-wrap">
    <div class="analytics-container">
        <!-- Header -->
        <div class="analytics-header">
            <h1>📊 Detection Analytics & Reports</h1>
            <p>View your quail and feed detection history, statistics, and export reports</p>
        </div>

        <!-- Date Filter -->
        <div class="date-filter">
            <form method="GET" action="{{ route('detection-analytics') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end; width: 100%;">
                <div class="filter-group">
                    <label>From Date</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" required>
                </div>
                <div class="filter-group">
                    <label>To Date</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" required>
                </div>
                <button type="submit" class="filter-btn">🔍 Filter</button>
            </form>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📈</div>
                <div class="stat-label">Total Detections</div>
                <div class="stat-value">{{ $totalDetections }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-label">Quails Detected</div>
                <div class="stat-value">{{ $successfulDetections }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">👤</div>
                <div class="stat-label">Human Detections</div>
                <div class="stat-value">{{ $humanDetections }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">❓</div>
                <div class="stat-label">Unknown Detections</div>
                <div class="stat-value">{{ $unknownDetections }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">🎯</div>
                <div class="stat-label">Avg Confidence</div>
                <div class="stat-value">{{ $avgConfidence ? round($avgConfidence * 100) : 0 }}%</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">🐔</div>
                <div class="stat-label">Breeds Detected</div>
                <div class="stat-value">{{ $breedDistribution->count() }}</div>
            </div>
        </div>

        <!-- Breed Distribution -->
        @if($breedDistribution->count() > 0)
        <div class="charts-section">
            <h2>🐔 Quail Breed Distribution</h2>
            <div style="max-width: 100%;">
                @foreach($breedDistribution as $item)
                    <div class="breed-item">
                        <span class="breed-name">{{ $item->breed?->name ?? 'Unknown Breed' }}</span>
                        <span class="breed-count">{{ $item->count }} detected</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Feed Compatibility Stats -->
        <div class="charts-section">
            <h2>🍽️ Feed Compatibility Summary</h2>
            <div class="chart-group">
                <div class="stat-card">
                    <div class="stat-icon">✅</div>
                    <div class="stat-label">Suitable Feeds</div>
                    <div class="stat-value">{{ $feedCompatible }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⚠️</div>
                    <div class="stat-label">Caution Feeds</div>
                    <div class="stat-value">{{ $feedCaution }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">❌</div>
                    <div class="stat-label">Unsuitable Feeds</div>
                    <div class="stat-value">{{ $feedIncompatible }}</div>
                </div>
            </div>
        </div>

        <!-- Top Locations -->
        @if($topLocations->count() > 0)
        <div class="charts-section">
            <h2>📍 Top Detection Locations</h2>
            <div>
                @foreach($topLocations as $location)
                    <div class="breed-item">
                        <span class="breed-name">{{ $location->location }}</span>
                        <span class="breed-count">{{ $location->count }} times</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Export Section -->
        <div class="export-section">
            <h2 style="color: #6d4c41; margin-bottom: 1.5rem;">📥 Export Reports</h2>
            <p style="color: #8d6e63; margin-bottom: 1.5rem;">Download your detection history in various formats</p>
            
            <form method="GET" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
                <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                <input type="hidden" name="date_to" value="{{ $dateTo }}">
                
                <button formaction="{{ route('detection-analytics.export-csv') }}" type="submit" class="export-btn">
                    📄 Export as CSV
                </button>
                <button formaction="{{ route('detection-analytics.export-pdf') }}" type="submit" class="export-btn">
                    📋 Export as PDF
                </button>
                <button formaction="{{ route('detection-analytics.feed-export-csv') }}" type="submit" class="export-btn">
                    🍽️ Feed Report CSV
                </button>
            </form>
        </div>

        <!-- Recent Detections -->
        <div class="charts-section" style="margin-top: 2rem;">
            <h2>📋 Recent Detections</h2>
            <div>
                @if($recentDetections->count() > 0)
                    @foreach($recentDetections as $detection)
                        <div class="breed-item">
                            <div>
                                <div class="breed-name">
                                    @if($detection->detection_type === 'quail')
                                        ✅ {{ $detection->breed?->name ?? 'Unknown Breed' }}
                                    @elseif($detection->detection_type === 'human')
                                        👤 Human Detected
                                    @else
                                        ❓ Unknown Object
                                    @endif
                                </div>
                                <small style="color: #999;">
                                    {{ $detection->created_at->diffForHumans() }}
                                    @if($detection->location) • {{ $detection->location }} @endif
                                    @if($detection->confidence) • {{ round($detection->confidence * 100) }}% confidence @endif
                                </small>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div style="text-align: center; padding: 2rem; color: #a1887f;">
                        No detections found in this period
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

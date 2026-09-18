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

    /* Notification Bell Styles */
    .notification-bell {
        position: relative;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .bell-icon {
        transition: all 0.3s ease;
        animation: bellRing 0.5s ease-in-out infinite;
    }

    .notification-bell:hover .bell-icon {
        stroke-width: 2.5;
    }

    @keyframes bellRing {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(-10deg); }
        75% { transform: rotate(10deg); }
    }

    .notification-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 100%);
        color: white;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
        animation: badgePulse 2s infinite;
    }

    @keyframes badgePulse {
        0%, 100% { box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3); }
        50% { box-shadow: 0 2px 12px rgba(255, 107, 107, 0.5); }
    }

    .notification-dropdown {
        position: fixed;
        top: 80px;
        right: 1.5rem;
        width: 380px;
        max-height: 450px;
        overflow-y: auto;
        background: linear-gradient(135deg, #fffdfa 0%, #f5f0eb 100%);
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(109,76,65,0.15), 0 0 1px rgba(109,76,65,0.1);
        border: 2px solid #efebe9;
        z-index: 1100;
        display: none;
    }

    .notification-dropdown.show {
        display: block;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-15px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .notification-header {
        padding: 1.25rem 1.5rem;
        background: linear-gradient(135deg, #efebe9 0%, #e8ddd8 100%);
        border-bottom: 2px solid #d7ccc8;
        border-radius: 14px 14px 0 0;
        font-weight: 700;
        color: #6d4c41;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 1rem;
    }

    /* Category Tabs */
    .category-tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }
    .category-tab {
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid #a1887f;
        background: #fffdfa;
        color: #6d4c41;
        box-shadow: 0 4px 12px rgba(141, 110, 99, 0.10);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .category-tab:hover {
        background: #f8ede3;
        box-shadow: 0 6px 16px rgba(141, 110, 99, 0.18);
    }
    .category-tab.active {
        background: #a1887f;
        color: #fffdfa;
        border-color: #a1887f;
        box-shadow: 0 6px 16px rgba(141, 110, 99, 0.18), 0 0 20px rgba(161, 136, 127, 0.5);
    }
    .category-icon {
        font-size: 1rem;
    }

    /* Hidden category content */
    .category-content {
        display: none;
    }
    .category-content.active {
        display: block;
    }
    .container {
        max-width: 100%;
        margin: 0 auto;
        padding: 2.5rem 3rem;
    }
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
    .section-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
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
        box-sizing: border-box;
    }
    .input-field:focus {
        outline: none;
        border-color: #a1887f;
        background: white;
    }
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

    /* Sidebar content wrapper - matching dashboard behavior */
    .sidebar-content-wrap {
        margin-left: 300px;
        transition: margin-left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        padding-top: 0;
    }
    .sidebar-content-wrap.expanded {
        margin-left: 0;
        padding-top: 80px;
    }

    /* Stats cards grid - More columns when expanded */
    .feeder-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        transition: grid-template-columns 0.4s ease;
    }
    .sidebar-content-wrap.expanded .feeder-stats-grid {
        grid-template-columns: repeat(4, 1fr);
    }

    /* Expanded state - Full width content */
    .sidebar-content-wrap.expanded .container {
        max-width: 1600px;
        margin: 0 auto;
        padding-left: 2rem;
        padding-right: 2rem;
    }

    /* Breed cards - side by side when expanded */
    .breed-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        transition: grid-template-columns 0.4s ease;
    }
    .sidebar-content-wrap.expanded .breed-cards-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    @media (max-width: 1200px) {
        .feeder-stats-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        .sidebar-content-wrap.expanded .feeder-stats-grid {
            grid-template-columns: repeat(3, 1fr) !important;
        }
    }

    @media (max-width: 768px) {
        .sidebar-content-wrap {
            margin-left: 0 !important;
            padding-top: 70px;
        }
        .feeder-stats-grid,
        .breed-cards-grid {
            grid-template-columns: 1fr !important;
        }
        .sidebar-content-wrap.expanded .feeder-stats-grid,
        .sidebar-content-wrap.expanded .breed-cards-grid {
            grid-template-columns: 1fr !important;
        }
        .container { padding: 1.5rem 1rem; }
        .nav-menu { display: none; }
    }</style>

<div id="main-content-wrap" class="sidebar-content-wrap">
<!-- Main Content -->
<div class="container">
    <div class="page-header">
        <h1 class="page-title">Feeder Management</h1>
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
        <div class="breed-cards-grid" style="margin-top: 1rem;">
            @foreach($uniqueBreeds as $index => $breed)
            <div style="padding: 1rem; background: linear-gradient(135deg, #fff9f5 0%, #fff7f2 100%); border-radius: 12px; border-left: 4px solid #a1887f;">
                <div style="font-weight: 700; color: #6d4c41; font-size: 0.95rem; margin-bottom: 0.75rem;">Breed #{{ $index + 1 }}: {{ $breed->name }}</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.8rem; color: #5d4037;">
                    <div><strong>Weight:</strong> {{ $breed->mature_weight }}g</div>
                    <div><strong>Daily Feed:</strong> ~{{ round($breed->mature_weight * 0.25) }}g/bird</div>
                    <div><strong>Feed/Egg:</strong> {{ round(($breed->egg_production_rate / 365) * 12) }}g</div>
                    <div><strong>Eggs/Year:</strong> {{ $breed->egg_production_rate }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
        @endisset
    </div>

    <!-- Category Tabs -->
    <div class="category-tabs">
        <button class="category-tab active" onclick="showCategory('schedule')">
            Daily Feeding
        </button>
        <button class="category-tab" onclick="showCategory('manual')">
            Manual Feed
        </button>
        <button class="category-tab" onclick="showCategory('history')">
            Feed History
        </button>
        <div id="feed-brand-toggle" style="margin-left:auto;display:flex;align-items:center;gap:0.75rem;">
                    <span style="font-size:0.875rem;font-weight:600;color:#6d4c41;">Feed using: <span id="feed-brand-text">{{ $feedBrand }}</span></span>
                    <form method="POST" action="{{ route('feeder.toggle') }}" style="display:inline;">
                        @csrf
                        <button type="submit" title="Toggle to {{ $feedBrand === 'Quail Layer Smash' ? 'Jedstar' : 'Quail Layer Smash' }}"
                            style="width:52px;height:28px;border-radius:14px;border:none;cursor:pointer;position:relative;
                                   transition:all 0.3s ease;
                                   background:#a1887f;box-shadow:0 2px 6px rgba(161,136,127,0.4);">
                            <span style="position:absolute;top:3px;width:22px;height:22px;border-radius:50%;background:white;box-shadow:0 2px 6px rgba(0,0,0,0.2);transition:left 0.3s ease;
                                   left:{{ $feedBrand === 'Quail Layer Smash' ? '27px' : '3px' }};"></span>
                        </button>
                    </form>
        </div>
    </div>

    <!-- Feeding Info Tab -->
    <div id="schedule" class="category-content active">

        <!-- Stats Cards -->
        <div class="feeder-stats-grid">
            <div class="section-card" style="padding:1.5rem;text-align:center;border-top:4px solid #a1887f;">
                <div style="font-size:2rem;font-weight:800;color:#6d4c41;">{{ $fedToday }}<span style="font-size:1.1rem;color:#a1887f;">/3</span></div>
                <div style="font-size:0.85rem;font-weight:600;color:#8d6e63;margin-top:0.25rem;">Fed Today</div>
            </div>
            <div class="section-card" style="padding:1.5rem;text-align:center;border-top:4px solid #8d6e63;">
                <div style="font-size:2rem;font-weight:800;color:#6d4c41;">{{ $fedThisWeek }}</div>
                <div style="font-size:0.85rem;font-weight:600;color:#8d6e63;margin-top:0.25rem;">Fed This Week</div>
            </div>
            <div class="section-card" style="padding:1.5rem;text-align:center;border-top:4px solid #6d4c41;">
                <div style="font-size:1.1rem;font-weight:800;color:#6d4c41;">{{ $feedBrand }}</div>
                <div style="font-size:0.85rem;font-weight:600;color:#8d6e63;margin-top:0.25rem;">Feed Brand</div>
            </div>
        </div>

        <!-- Food Level Card -->
        <div class="section-card" id="food-level-card" style="margin-top:1.5rem;padding:1.5rem 2rem;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                <div style="font-weight:700;color:#6d4c41;font-size:1rem;">Food Level</div>
                <span id="food-level-status-pill" class="status-pill" style="background:#d1fae5;color:#065f46;">Loading...</span>
            </div>
            <div style="background:#efebe9;border-radius:99px;height:18px;overflow:hidden;">
                <div id="food-level-bar" style="height:100%;width:0%;background:linear-gradient(90deg,#a1887f,#6d4c41);border-radius:99px;transition:width 0.8s ease;"></div>
            </div>
            <div style="display:flex;justify-content:center;margin-top:0.5rem;">
                <span id="food-level-pct" style="font-size:1.1rem;font-weight:800;color:#6d4c41;">--%</span>
            </div>
            <div id="food-level-updated" style="font-size:0.75rem;color:#a1887f;margin-top:0.25rem;text-align:right;"></div>
        </div>

        <!-- Next Feeding Card -->
        <div class="section-card" style="text-align:center;padding:2.5rem 2rem;margin-top:1.5rem;">
            <div style="font-size:0.95rem;font-weight:600;color:#8d6e63;margin-bottom:0.5rem;letter-spacing:1px;">NEXT FEEDING IN</div>
            <div id="countdown-display" style="font-size:3rem;font-weight:800;color:#6d4c41;letter-spacing:4px;font-variant-numeric:tabular-nums;">--:--:--</div>
            <div style="font-size:1rem;font-weight:600;color:#a1887f;margin-top:0.5rem;">
                {{ $nextFeeding->format('g:i A') }} &mdash; {{ $nextFeeding->isToday() ? 'Today' : 'Tomorrow' }}
            </div>
            <div id="realtime-date" style="font-size:0.85rem;font-weight:700;color:#a1887f;margin-top:0.35rem;"></div>
        </div>

        <!-- Visual Timeline -->
        <div class="section-card" style="margin-top:1.5rem;">
            <h3 class="section-title">Daily Feeding Schedule</h3>

            <div style="display:flex;align-items:flex-start;justify-content:center;gap:0;margin:1.5rem 0 2rem;position:relative;">
                @foreach($feedTimes as $time)
                @php
                    $feedTime = \Carbon\Carbon::createFromFormat('H:i', $time)->setDateFrom(now());
                    $isPast   = $feedTime->lt(now());
                    $isNext   = $nextFeeding->format('H:i') === $time && $nextFeeding->isToday();
                    $isLast   = $time === end($feedTimes);
                    $label    = $feedTimesWithLabels[$time] ?? $time;
                @endphp
                <div style="display:flex;align-items:center;flex:1;">
                    <!-- Node -->
                    <div style="display:flex;flex-direction:column;align-items:center;flex:1;">
                        <div class="timeline-node {{ $isPast ? 'node-done' : ($isNext ? 'node-next' : 'node-upcoming') }}">
                            @if($isPast)
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            @elseif($isNext)
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            @else
                                @php
                                    $hour = (int)substr($time, 0, 2);
                                @endphp
                                @if($hour < 12)
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                                @elseif($hour < 17)
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"></path></svg>
                                @else
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                                @endif
                            @endif
                        </div>
                        <div style="margin-top:0.75rem;font-weight:700;color:#4e342e;font-size:0.95rem;">{!! $label !!}</div>
                        <div style="font-size:0.78rem;color:#8d6e63;margin-top:0.2rem;">20s &bull; ~250g</div>
                        @if($isPast)
                            <div class="tl-badge badge-done">Done</div>
                        @elseif($isNext)
                            <div class="tl-badge badge-next">Next</div>
                        @else
                            <div class="tl-badge badge-upcoming">Upcoming</div>
                        @endif
                    </div>
                    <!-- Connector line (not after last) -->
                    @if(!$isLast)
                    <div style="flex:1;height:3px;background:{{ $isPast ? 'linear-gradient(90deg,#a1887f,#a1887f)' : '#efebe9' }};margin-bottom:3rem;border-radius:2px;"></div>
                    @endif
                </div>
                @endforeach
            </div>

        </div>
    </div>

<style>
.timeline-node {
    width: 56px; height: 56px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    font-weight: 800;
    box-shadow: 0 4px 12px rgba(109,76,65,0.15);
    transition: all 0.3s;
}
.node-done {
    background: linear-gradient(135deg, #a5d6a7, #66bb6a);
    color: white;
    font-size: 1.5rem;
    animation: checkPop 0.4s ease;
}
@keyframes checkPop {
    0%   { transform: scale(0.7); }
    70%  { transform: scale(1.15); }
    100% { transform: scale(1); }
}
.node-next {
    background: linear-gradient(135deg, #ffe082, #ffca28);
    color: #6d4c41;
    animation: pulse-next 1.5s ease-in-out infinite;
    box-shadow: 0 0 0 0 rgba(255,202,40,0.5);
}
@keyframes pulse-next {
    0%,100% { box-shadow: 0 0 0 0 rgba(255,202,40,0.5); }
    50%      { box-shadow: 0 0 0 10px rgba(255,202,40,0); }
}
.node-upcoming {
    background: #efebe9;
    color: #a1887f;
}
.tl-badge {
    margin-top: 0.5rem;
    padding: 0.2rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
}
.badge-done     { background:#d1fae5; color:#065f46; }
.badge-next     { background:#fef3c7; color:#92400e; }
.badge-upcoming { background:#efebe9; color:#6d4c41; }
</style>


    <!-- Manual Feed Tab -->
    <div id="manual" class="category-content">
        <div class="section-card">
            <h3 class="section-title">Manual Feed Control</h3>
            <p style="color:#8d6e63;margin-bottom:1.5rem;font-size:0.95rem;">Manually trigger feeding outside of scheduled times. Use this for testing or emergency feeding.</p>
            
            <form id="manualFeedForm" method="POST" action="{{ route('feeder.feed') }}">
                @csrf
                <div class="input-group">
                    <label class="input-label">Cage Number</label>
                    <select name="cage_number" class="input-field" required>
                        <option value="1">Cage 1</option>
                        <option value="2">Cage 2</option>
                        <option value="3">Cage 3</option>
                        <option value="4">Cage 4</option>
                        <option value="5">Cage 5</option>
                        <option value="6">Cage 6</option>
                        <option value="7">Cage 7</option>
                        <option value="8">Cage 8</option>
                        <option value="9">Cage 9</option>
                        <option value="10">Cage 10</option>
                    </select>
                </div>
                
                <div class="input-group">
                    <label class="input-label">Duration (seconds)</label>
                    <select name="duration" class="input-field" required>
                        <option value="5">5 seconds (~62g)</option>
                        <option value="10" selected>10 seconds (~125g)</option>
                        <option value="15">15 seconds (~187g)</option>
                        <option value="20">20 seconds (~250g)</option>
                        <option value="30">30 seconds (~375g)</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width:100%;font-size:1.1rem;padding:1rem;">
                    Feed Now
                </button>
            </form>
            
            <div style="margin-top:1.5rem;padding:1rem;background:#f5f0eb;border:2px solid #a1887f;border-radius:12px;">
                <div style="font-weight:700;color:#6d4c41;margin-bottom:0.5rem;display:flex;align-items:center;gap:0.5rem;">
                    Feeding Features
                </div>
                <ul style="color:#6d4c41;font-size:0.875rem;margin:0;padding-left:1.5rem;line-height:1.6;">
                    <li>Manual feeding shows dispensing animation</li>
                    <li>Real-time progress bar and countdown timer</li>
                    <li>Avoid overfeeding - recommended 3 times daily</li>
                    <li>Each feeding session dispenses approximately 12.5g per second</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="confirm-modal-overlay" id="deleteHistoryModal">
        <div class="confirm-modal">
            <div class="confirm-modal-title">Delete Feed History</div>
            <div style="margin: 1.5rem 0; font-size:1rem;color:#4e342e;">
                <div><b>Date & Time:</b> <span id="deleteHistoryTime"></span></div>
                <div><b>Duration:</b> <span id="deleteHistoryDuration"></span> seconds</div>
                <div><b>Cage:</b> <span id="deleteHistoryCage"></span></div>
            </div>
            <div class="confirm-modal-buttons">
                <button type="button" class="confirm-modal-btn cancel" onclick="hideDeleteHistoryModal()">Cancel</button>
                <button type="button" class="confirm-modal-btn confirm" onclick="submitDeleteHistoryForm()">Yes, Delete</button>
            </div>
        </div>
    </div>

    <!-- Feed History Tab -->
    <div id="history" class="category-content">
        <div class="section-card">
            <h3 class="section-title">Feed History</h3>
            
            <!-- Past Feed History Section -->
            <table style="width:100%;border-collapse:collapse;table-layout:fixed;" id="history-table" {{ $feedHistory->count() ? '' : 'style="display:none;width:100%;border-collapse:collapse;table-layout:fixed;"' }}>
                <thead>
                    <tr style="border-bottom:2px solid #efebe9;">
                        <th style="width:25%;text-align:left;padding:0.75rem 1rem;color:#8d6e63;font-weight:600;font-size:0.875rem;">Date & Time</th>
                        <th style="width:10%;text-align:left;padding:0.75rem 1rem;color:#8d6e63;font-weight:600;font-size:0.875rem;">Cage</th>
                        <th style="width:12%;text-align:left;padding:0.75rem 1rem;color:#8d6e63;font-weight:600;font-size:0.875rem;">Type</th>
                        <th style="width:13%;text-align:left;padding:0.75rem 1rem;color:#8d6e63;font-weight:600;font-size:0.875rem;">Brand</th>
                        <th style="width:12%;text-align:left;padding:0.75rem 1rem;color:#8d6e63;font-weight:600;font-size:0.875rem;">Duration</th>
                        <th style="width:13%;text-align:left;padding:0.75rem 1rem;color:#8d6e63;font-weight:600;font-size:0.875rem;">Status</th>
                        <th style="width:15%;text-align:left;padding:0.75rem 1rem;color:#8d6e63;font-weight:600;font-size:0.875rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($feedHistory as $h)
                    <tr style="border-bottom:1px solid #efebe9;">
                        <td style="padding:0.75rem 1rem;color:#4e342e;font-weight:600;vertical-align:middle;">{{ \Carbon\Carbon::parse($h->fed_at)->format('M d, Y h:i A') }}</td>
                        <td style="padding:0.75rem 1rem;color:#6d4c41;vertical-align:middle;">
                            <span style="font-size:0.875rem;font-weight:600;background:#f5f0eb;padding:0.25rem 0.5rem;border-radius:6px;">{{ $h->cage_number ?? 1 }}</span>
                        </td>
                        <td style="padding:0.75rem 1rem;vertical-align:middle;">
                            <span class="status-pill" style="background:{{ $h->feed_type==='scheduled'?'#e3f2fd':'#fff3e0' }};color:{{ $h->feed_type==='scheduled'?'#1565c0':'#e65100' }};text-transform:capitalize;">{{ ucfirst($h->feed_type) }}</span>
                        </td>
                        <td style="padding:0.75rem 1rem;color:#6d4c41;vertical-align:middle;">{{ $h->feed_brand ?? 'N/A' }}</td>
                        <td style="padding:0.75rem 1rem;color:#6d4c41;vertical-align:middle;">{{ $h->duration }}s</td>
                        <td style="padding:0.75rem 1rem;vertical-align:middle;">
                            <span class="status-pill" style="background:{{ $h->status==='completed'?'#d1fae5':($h->status==='failed'?'#fee2e2':'#efebe9') }};color:{{ $h->status==='completed'?'#065f46':($h->status==='failed'?'#991b1b':'#6d4c41') }};">{{ ucfirst($h->status) }}</span>
                        </td>
                        <td style="padding:0.75rem 1rem;vertical-align:middle;">
                            <form method="POST" action="{{ route('feeder.history.delete', $h->id) }}" class="delete-history-form" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-danger" onclick="showDeleteHistoryModal(this, '{{ \Carbon\Carbon::parse($h->fed_at)->format('M d, Y h:i A') }}', '{{ $h->duration }}', '{{ $h->cage_number ?? 1 }}')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <p class="no-history" style="color:#8d6e63;margin:0;text-align:center;{{ $feedHistory->count() ? 'display:none;' : '' }}">No feed history yet.</p>
        </div>
    </div>
</div>

<style>
.confirm-modal-overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}
.confirm-modal-overlay.show { display: flex; }
.confirm-modal {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 20px 60px rgba(109,76,65,0.3);
}
@keyframes confirmSlideIn {
    from { transform: scale(0.9); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
.confirm-modal-icon { font-size: 3rem; margin-bottom: 1rem; }
.confirm-modal-title { font-size: 1.25rem; font-weight: 700; color: #4e342e; margin-bottom: 1rem; }
.confirm-modal-message { color: #6d4c41; margin-bottom: 1.5rem; line-height: 1.5; }
.confirm-modal-buttons { display: flex; gap: 1rem; justify-content: flex-end; }
.confirm-modal-btn { padding: 0.75rem 1.5rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.confirm-modal-btn.cancel { background: #e0e0e0; color: #666; }
.confirm-modal-btn.cancel:hover { background: #d0d0d0; }
.confirm-modal-btn.confirm { background: linear-gradient(135deg, #6d4c41 0%, #a1887f 100%); color: white; }
.confirm-modal-btn.confirm:hover { box-shadow: 0 4px 12px rgba(109,76,65,0.3); }
</style>

<script>
    // Countdown to next feeding
    (function() {
        const feedTimes = @json($feedTimes);
        const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];

        function getNextFeeding() {
            const now = new Date();
            for (const t of feedTimes) {
                const [h, m] = t.split(':').map(Number);
                const candidate = new Date(now);
                candidate.setHours(h, m, 0, 0);
                if (candidate > now) return candidate;
            }
            const tomorrow = new Date(now);
            tomorrow.setDate(tomorrow.getDate() + 1);
            const [h, m] = feedTimes[0].split(':').map(Number);
            tomorrow.setHours(h, m, 0, 0);
            return tomorrow;
        }

        function updateCountdown() {
            const el = document.getElementById('countdown-display');
            if (!el) return;
            const diff = Math.max(0, getNextFeeding() - new Date());
            const h = String(Math.floor(diff / 3600000)).padStart(2, '0');
            const m = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
            const s = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
            el.textContent = h + ':' + m + ':' + s;
        }

        function updateDate() {
            const el = document.getElementById('realtime-date');
            if (!el) return;
            const now = new Date();
            el.textContent = months[now.getMonth()] + ' ' + now.getDate() + ', ' + now.getFullYear();
        }

        updateCountdown();
        updateDate();
        setInterval(updateCountdown, 1000);
        setInterval(updateDate, 1000);
    })();

    function showCategory(categoryId) {
        document.querySelectorAll('.category-content').forEach(content => {
            content.classList.remove('active');
        });
        document.querySelectorAll('.category-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        document.getElementById(categoryId).classList.add('active');
        if (event && event.currentTarget) {
            event.currentTarget.classList.add('active');
        } else {
            document.querySelectorAll('.category-tab').forEach(tab => {
                if (tab.getAttribute('onclick').includes(categoryId)) {
                    tab.classList.add('active');
                }
            });
        }
        // Hide toggle on history tab, show on schedule tab
        const toggle = document.getElementById('feed-brand-toggle');
        if (toggle) toggle.style.display = categoryId === 'history' ? 'none' : 'flex';
        
        // Update toggle text after page reload/redirect
        setTimeout(() => {
            const brandText = document.getElementById('feed-brand-text');
            if (brandText) {
                // Trigger reflow to show updated server-side value
                brandText.style.opacity = '0.99';
                setTimeout(() => { brandText.style.opacity = '1'; }, 50);
            }
        }, 100);
        localStorage.setItem('feederActiveTab', categoryId);
        window.scrollTo({ top: 200, behavior: 'smooth' });
    }

    function switchTab(name) {
        // Legacy function for backward compatibility
        showCategory(name);
    }

    // Restore active tab from localStorage on page load
    (function() {
        const savedTab = localStorage.getItem('feederActiveTab');
        
        if (savedTab && document.getElementById(savedTab)) {
            showCategory(savedTab);
        } else if (window.location.hash === '#history') {
            showCategory('history');
        }
    })();

    function playNotifSound(type) {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            if (type === 'save') {
                // Two ascending tones � success
                [523, 659].forEach((freq, i) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.connect(gain); gain.connect(ctx.destination);
                    osc.type = 'sine';
                    const t = ctx.currentTime + i * 0.18;
                    osc.frequency.setValueAtTime(freq, t);
                    gain.gain.setValueAtTime(0, t);
                    gain.gain.linearRampToValueAtTime(0.4, t + 0.03);
                    gain.gain.exponentialRampToValueAtTime(0.001, t + 0.35);
                    osc.start(t); osc.stop(t + 0.35);
                });
            } else {
                // Single low tone � delete
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain); gain.connect(ctx.destination);
                osc.type = 'sine';
                osc.frequency.setValueAtTime(330, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(220, ctx.currentTime + 0.3);
                gain.gain.setValueAtTime(0.4, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
                osc.start(); osc.stop(ctx.currentTime + 0.4);
            }
        } catch(e) {}
    }

    // Handle session flash messages as notifications
    function showSessionNotifications() {
        if (typeof notificationSystem === 'undefined' || !notificationSystem) {
            // Retry if notificationSystem isn't ready yet
            setTimeout(showSessionNotifications, 150);
            return;
        }
        
        @if(session('success'))
            notificationSystem.success("{{ session('success') }}", '?');
        @endif
        @if(session('error'))
            notificationSystem.error("{{ session('error') }}", '?');
        @endif
    }
    
    let deleteFormToSubmit = null;

    // Handle manual feed form submission
let manualFeedInFlight = false;

document.getElementById('manualFeedForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    if (manualFeedInFlight) {
        console.warn('[Manual Feed] Duplicate click ignored');
        return;
    }

    manualFeedInFlight = true;

    const form = this;
    const submitButton = form.querySelector('button[type="submit"]');

    if (submitButton) {
        submitButton.disabled = true;
    }

    console.log('[Manual Feed] Feed Now clicked');

    const formData = new FormData(form);
    const duration = parseInt(formData.get('duration'), 10) || 5;
    const cage = parseInt(formData.get('cage_number'), 10) || 1;

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(async response => {
        const data = await response.json();

        if (!response.ok) {
            throw new Error(
                data.message || 'Failed to start feeding.'
            );
        }

        return data;
    })
    .then(data => {

        console.log('[Manual Feed] Server response:', data);

        // ============================================
        // FEEDING STARTED / QUEUED
        // ============================================
        if (data.status === 'feeding_now' || data.status === 'queued') {

            if (typeof notificationSystem !== 'undefined') {
                notificationSystem.feeder(
                    '🍲 FEEDING NOW — Manual feeding started for Cage ' +
                    cage +
                    ' (' +
                    duration +
                    ' sec)',
                    '✅'
                );
            }

            playNotifSound('save');

            // START ANIMATION FOR BOTH:
            // feeding_now = Serial mode
            // queued      = WiFi/ESP32 mode
            if (typeof window.showFeedingAnimation === 'function') {

                console.log(
                    '[Manual Feed] Starting feeding animation:',
                    duration,
                    'seconds, Cage',
                    cage,
                    'Status:',
                    data.status
                );

                window.showFeedingAnimation(
                    duration,
                    cage
                );
            }

        // ============================================
        // FEEDING SKIPPED
        // ============================================
        } else if (data.status === 'skipped') {

            if (typeof notificationSystem !== 'undefined') {
                notificationSystem.warning(
                    data.message ||
                    'Feeding skipped — another feed is already in progress.',
                    '⚠️'
                );
            }

            playNotifSound('delete');

            manualFeedInFlight = false;

            if (submitButton) {
                submitButton.disabled = false;
            }

        // ============================================
        // UNEXPECTED RESPONSE
        // ============================================
        } else {

            console.error(
                '[Manual Feed] Unexpected server response:',
                data
            );

            if (typeof notificationSystem !== 'undefined') {
                notificationSystem.error(
                    data.message ||
                    'Failed to start feeding.',
                    '❌'
                );
            }

            manualFeedInFlight = false;

            if (submitButton) {
                submitButton.disabled = false;
            }
        }
    })
    .catch(err => {

        console.error(
            '[Manual Feed] Request error:',
            err
        );

        if (typeof notificationSystem !== 'undefined') {
            notificationSystem.error(
                'Connection error: ' + err.message,
                '❌'
            );
        }

        manualFeedInFlight = false;

        if (submitButton) {
            submitButton.disabled = false;
        }
    });
});

    function showDeleteScheduleModal(btn, time, cage) {
        document.getElementById('deleteScheduleTime').innerText = time;
        document.getElementById('deleteScheduleCage').innerText = 'Cage ' + cage;
        document.getElementById('deleteScheduleModal').classList.add('show');
        deleteFormToSubmit = btn.closest('form');
    }

    function hideDeleteScheduleModal() {
        document.getElementById('deleteScheduleModal').classList.remove('show');
        deleteFormToSubmit = null;
    }

    function submitDeleteScheduleForm() {
        if (deleteFormToSubmit) {
            playNotifSound('delete');
            deleteFormToSubmit.submit();
        }
        hideDeleteScheduleModal();
    }

    function triggerScheduleNow(scheduleId) {
        if (!confirm('Trigger this feeding schedule now?')) return;
        
        fetch('/feeder/schedules/' + scheduleId + '/trigger', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'ok' || data.status === 'queued') {
                if (typeof notificationSystem !== 'undefined') {
                    notificationSystem.success('Feeding triggered successfully!', '✅');
                }
                playNotifSound('save');
                setTimeout(() => location.reload(), 1500);
            } else {
                if (typeof notificationSystem !== 'undefined') {
                    notificationSystem.error('Failed to trigger feeding', '❌');
                }
            }
        })
        .catch(err => {
            if (typeof notificationSystem !== 'undefined') {
                notificationSystem.error('Connection error: ' + err.message, '❌');
            }
        });
    }

    function showDeleteHistoryModal(btn, time, duration, cage) {
        document.getElementById('deleteHistoryTime').innerText = time;
        document.getElementById('deleteHistoryDuration').innerText = duration;
        document.getElementById('deleteHistoryCage').innerText = 'Cage ' + cage;
        document.getElementById('deleteHistoryModal').classList.add('show');
        deleteFormToSubmit = btn.closest('form');
    }

    function hideDeleteHistoryModal() {
        document.getElementById('deleteHistoryModal').classList.remove('show');
        deleteFormToSubmit = null;
    }

    function submitDeleteHistoryForm() {
        if (deleteFormToSubmit) {
            playNotifSound('delete');
            deleteFormToSubmit.submit();
        }
        hideDeleteHistoryModal();
    }

    // Food Level Polling — refresh the latest ultrasonic result near real time.
    (function() {
        let lastFoodLevelState = null;

        function updateFoodLevel() {
            fetch('/api/food-level/current', { cache: 'no-store' })
                .then(r => r.json())
                .then(function(data) {
                    const bar    = document.getElementById('food-level-bar');
                    const pct    = document.getElementById('food-level-pct');
                    const pill   = document.getElementById('food-level-status-pill');
                    const upd    = document.getElementById('food-level-updated');
                    if (!bar) return;

                    const level  = data.level ?? 0;
                    const detected = data.status !== 'empty';
                    bar.style.width = level + '%';
                    if (pct) pct.textContent = detected ? level + '%' : 'No food';

                    // Color based on status
                    if (data.status === 'empty') {
                        bar.style.background = 'linear-gradient(90deg,#ef5350,#c62828)';
                        if (pill) { pill.style.background='#fee2e2'; pill.style.color='#991b1b'; pill.textContent='Empty'; }
                    } else if (data.status === 'low') {
                        bar.style.background = 'linear-gradient(90deg,#ffa726,#e65100)';
                        if (pill) { pill.style.background='#fff3e0'; pill.style.color='#e65100'; pill.textContent='Low'; }
                    } else {
                        bar.style.background = 'linear-gradient(90deg,#a1887f,#6d4c41)';
                        if (pill) { pill.style.background='#d1fae5'; pill.style.color='#065f46'; pill.textContent='Normal'; }
                    }

                    if (upd && data.last_updated) {
                        upd.textContent = 'Updated: ' + new Date(data.last_updated).toLocaleTimeString();
                    }

                    // Determine notification state based on percentage
                    let currentState = 'normal';
                    if (level >= 0 && level <= 10) {
                        currentState = 'critical';
                    } else if (level >= 20 && level <= 30) {
                        currentState = 'low';
                    } else if (level >= 80 && level <= 100) {
                        currentState = 'refilled';
                    }

                    // Only notify once per state change
                    if (currentState !== lastFoodLevelState) {
                        lastFoodLevelState = currentState;

                        if (currentState === 'normal') return;
                        if (typeof notificationSystem === 'undefined') return;

                        let title, message, type;
                        if (currentState === 'critical') {
                            title = 'CRITICAL FOOD LEVEL';
                            message = 'Food is almost empty! Please refill immediately.';
                            type = 'error';
                        } else if (currentState === 'low') {
                            title = 'LOW FOOD LEVEL';
                            message = 'Food is running low in the feeder. Please refill the food soon.';
                            type = 'warning';
                        } else if (currentState === 'refilled') {
                            title = 'FOOD REFILLED';
                            message = 'Thank you! The feeder is ready again.';
                            type = 'success';
                        }

                        const fullMessage = title + '\n' + message + '\n' + currentState.toUpperCase() + ' — ' + level + '%';
                        notificationSystem.addNotification(fullMessage, type, '⚠️');
                    }
                })
                .catch(function() {});
        }
        updateFoodLevel();
        setInterval(updateFoodLevel, 2000);
    })();

    // Start checking for notifications - only once
    let notificationsShown = false;
    function initNotifications() {
        if (notificationsShown) return; // Prevent duplicate calls
        notificationsShown = true;
        // Small delay to ensure notificationSystem is initialized
        setTimeout(() => {
            showSessionNotifications();
            // Also notify dashboard of recent feeding events
            notifyRecentFeedingToDashboard();
        }, 200);
    }
    
    /**
     * Notify dashboard of recent feeding events
     * Gets the latest feed history and sends to dashboard notifications
     */
    function notifyRecentFeedingToDashboard() {
        if (typeof notifyFeederFeedingStatus !== 'function') {
            return; // Notification system not ready
        }
        
        // Get all table rows with feed history
        const historyTable = document.getElementById('history-table');
        if (!historyTable) return;
        
        const rows = historyTable.querySelectorAll('tbody tr');
        
        // Only notify for the very latest feed event (first row)
        if (rows.length > 0) {
            const firstRow = rows[0];
            const cells = firstRow.querySelectorAll('td');
            
            if (cells.length >= 5) {
                const timeCell = cells[0];
                const durationCell = cells[1];
                const breedCell = cells[2];
                const typeCell = cells[3];
                const statusCell = cells[4];
                
                // Extract data
                const status = statusCell?.innerText?.toLowerCase().trim() || 'unknown';
                const duration = durationCell?.innerText?.trim() || '0';
                const breed = breedCell?.innerText?.trim() || 'Quails';
                const feedType = typeCell?.innerText?.toLowerCase().trim() || 'manual';
                
                // Only notify if the status is recent (within last minute)
                // This is determined by checking if these are the most recent rows
                const isRecent = rows.length === 1 || 
                    (rows[0] && rows[0].classList && !rows[0].classList.contains('old-entry'));
                
                if (isRecent && (status === 'completed' || status === 'failed')) {
                    // Send to dashboard notification
                    try {
                        if (typeof addDashboardActivityNotification === 'function') {
                            const title = status === 'completed' 
                                ? `Feeding Completed - ${feedType.charAt(0).toUpperCase() + feedType.slice(1)}`
                                : `Feeding Failed - ${feedType.charAt(0).toUpperCase() + feedType.slice(1)}`;
                            const message = `${breed} feeding ${status} - ${duration} seconds`;
                            const icon = status === 'completed' ? '✅' : '❌';
                            
                            addDashboardActivityNotification('feeder', title, message);
                        }
                    } catch(e) {
                        // Silent fail if function not available
                    }
                }
            }
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNotifications);
    } else {
        initNotifications();
    }

    // Show dispensing animation with modal
    function showDispensingAnimation(duration, cage) {
        const overlay = document.getElementById('feeding-overlay');
        const countdown = document.getElementById('feed-countdown');
        const progressBar = document.getElementById('feed-progress-bar');
        const pctText = document.getElementById('feed-pct');
        const cageLabel = document.getElementById('feed-cage-label');

        overlay.classList.add('show');
        if (cageLabel) cageLabel.textContent = 'Cage ' + cage;

        const total = Math.max(1, duration);
        let elapsed = 0;
        if (countdown) countdown.textContent = total;

        const timer = setInterval(function () {
            elapsed++;
            const pct = Math.min(100, Math.round((elapsed / total) * 100));
            setBowlFill(pct);
            if (progressBar) progressBar.style.width = pct + '%';
            if (pctText) pctText.textContent = pct + '%';
            if (countdown) countdown.textContent = Math.max(0, total - elapsed);

            if (elapsed >= total) {
                clearInterval(timer);
                // Show completion
                setBowlFill(100);
                if (progressBar) progressBar.style.width = '100%';
                if (pctText) pctText.textContent = '100%';
                if (countdown) countdown.textContent = '0';
                
                // Wait 1 second then hide animation and show done modal
                setTimeout(() => {
                    overlay.classList.remove('show');
                    setBowlFill(0);
                    if (progressBar) progressBar.style.width = '0%';
                    if (pctText) pctText.textContent = '0%';
                    
                    // Show "Done Feeding" modal
                    showDoneFeedingModal(cage);
                }, 1000);
            }
        }, 1000);
    }

    function setBowlFill(pct) {
        const fill = document.getElementById('bowl-food-fill');
        if (!fill) return;
        const BOWL_EMPTY_Y = 120;
        const BOWL_MAX_H = 68;
        const h = (pct / 100) * BOWL_MAX_H;
        fill.setAttribute('y', BOWL_EMPTY_Y - h);
        fill.setAttribute('height', h);
    }

    function showDoneFeedingModal(cage) {
        // ONE-SHOT GUARD: kahit tawagin ito ng polling hide path o ng safety
        // timeout, ISANG BESES LANG ito lalabas kada feeding cycle.
        if (window.__doneFeedingPopupShown) return;
        window.__doneFeedingPopupShown = true;

        const modal = document.getElementById('done-feeding-modal');
        const cageText = document.getElementById('done-cage-text');
        if (cageText) cageText.textContent = 'Cage ' + cage;
        if (modal) {
            modal.style.display = 'flex';
            playNotifSound('save');
        }
        // Bell entry — DONE FEEDING (once per feeding cycle)
        if (typeof notificationSystem !== 'undefined') {
            notificationSystem.feeder('✅ DONE FEEDING — Feeding completed for Cage ' + cage, '✅');
        }
    }

    window.closeDoneFeedingModal = function() {
        const modal = document.getElementById('done-feeding-modal');
        if (modal) modal.style.display = 'none';
        // Reload to update feed history
        location.reload();
    };
</script>


<style>
/* -- Feeding Animation Overlay -- */
#feeding-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.6);
    z-index: 9000;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}
#feeding-overlay.show { display: flex; }

.feed-modal {
    background: linear-gradient(160deg, #fff9f5 0%, #f5ede6 100%);
    border-radius: 24px;
    padding: 2.5rem 2.5rem 2rem;
    text-align: center;
    box-shadow: 0 24px 64px rgba(109,76,65,0.3);
    width: 360px;
    position: relative;
}

/* Bowl */
.bowl-wrap {
    position: relative;
    width: 160px;
    height: 160px;
    margin: 0 auto 1.5rem;
}
.bowl-svg { width: 160px; height: 160px; }

/* Food fill inside bowl - clip path controlled by JS */
#bowl-food-fill {
    transition: height 0.5s ease, y 0.5s ease;
}

/* Falling food pellets */
.pellets {
    position: absolute;
    top: 0; left: 50%;
    transform: translateX(-50%);
    width: 40px;
    height: 60px;
    pointer-events: none;
    overflow: visible;
}
.pellet {
    position: absolute;
    border-radius: 50%;
    opacity: 0;
    animation: pellet-fall 0.8s ease-in infinite;
}
.pellet:nth-child(1){width:7px;height:7px;background:#c8a882;left:4px; animation-delay:0s;   animation-duration:0.75s;}
.pellet:nth-child(2){width:5px;height:5px;background:#a1887f;left:16px;animation-delay:0.2s; animation-duration:0.85s;}
.pellet:nth-child(3){width:6px;height:6px;background:#bcaaa4;left:28px;animation-delay:0.4s; animation-duration:0.70s;}
.pellet:nth-child(4){width:5px;height:5px;background:#d7ccc8;left:10px;animation-delay:0.6s; animation-duration:0.80s;}
.pellet:nth-child(5){width:7px;height:7px;background:#8d6e63;left:22px;animation-delay:0.1s; animation-duration:0.90s;}
@keyframes pellet-fall {
    0%   { opacity:0; transform:translateY(0) scale(0.7); }
    20%  { opacity:1; }
    80%  { opacity:0.9; }
    100% { opacity:0; transform:translateY(55px) scale(1); }
}

/* Percentage ring */
.pct-ring { position: relative; display: inline-block; margin-bottom: 0.5rem; }
.pct-text {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    font-weight: 800;
    color: #6d4c41;
}

/* Progress bar */
.progress-bar-wrap {
    width: 100%;
    height: 10px;
    background: #efebe9;
    border-radius: 99px;
    overflow: hidden;
    margin: 0.75rem 0 0.5rem;
}
.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #a1887f, #6d4c41);
    border-radius: 99px;
    transition: width 0.5s ease;
    width: 0%;
}

.feed-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #4e342e;
    margin-bottom: 0.25rem;
}
.feed-cage {
    font-size: 0.85rem;
    color: #8d6e63;
    margin-bottom: 0.25rem;
}
.feed-timer-label {
    font-size: 0.78rem;
    color: #a1887f;
    margin-top: 0.25rem;
}
.feed-countdown {
    font-size: 1rem;
    font-weight: 700;
    color: #6d4c41;
}
.status-dot {
    display: inline-block;
    width: 9px; height: 9px;
    border-radius: 50%;
    background: #4caf50;
    margin-right: 5px;
    animation: pulse-dot 1s ease-in-out infinite;
    vertical-align: middle;
}
@keyframes pulse-dot {
    0%,100% { box-shadow: 0 0 0 0 rgba(76,175,80,0.5); }
    50%      { box-shadow: 0 0 0 5px rgba(76,175,80,0); }
}
</style>

<!-- Feeding Notification Modal -->
<div id="feeding-notif-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:white;border-radius:20px;padding:2.5rem;text-align:center;box-shadow:0 24px 64px rgba(109,76,65,0.3);width:340px;">
        <div style="font-size:2.5rem;margin-bottom:0.75rem;">✅</div>
        <div style="font-size:1.4rem;font-weight:800;color:#2e7d32;margin-bottom:0.75rem;">Feeding Complete!</div>
        <div style="font-size:1rem;color:#6d4c41;margin-bottom:0.5rem;">The quails have been fed successfully.</div>
        <div id="feeding-notif-time" style="font-size:0.875rem;font-weight:600;color:#a1887f;margin-bottom:1.5rem;"></div>
        <button onclick="window.closeFeedingNotif()" style="background:linear-gradient(135deg,#4caf50,#66bb6a);color:white;border:none;padding:0.75rem 2rem;border-radius:10px;font-weight:700;font-size:1rem;cursor:pointer;width:100%;">OK, Got it</button>
    </div>
</div>

<!-- Done Feeding Modal -->
<div id="done-feeding-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:10000;align-items:center;justify-content:center;">
    <div style="background:white;border-radius:24px;padding:3rem 2.5rem;text-align:center;box-shadow:0 24px 64px rgba(109,76,65,0.4);width:380px;animation:modalBounce 0.5s ease;">
        <div style="font-size:1.6rem;font-weight:800;color:#6d4c41;margin-bottom:0.75rem;">Done Feeding!</div>
        <div style="font-size:1.1rem;color:#8d6e63;margin-bottom:0.5rem;font-weight:600;" id="done-cage-text">Cage 1</div>
        <div style="font-size:0.95rem;color:#a1887f;margin-bottom:2rem;">Feeding completed successfully</div>
        <button onclick="window.closeDoneFeedingModal()" style="background:linear-gradient(135deg,#a1887f,#8d6e63);color:white;border:none;padding:1rem 2.5rem;border-radius:12px;font-weight:700;font-size:1.1rem;cursor:pointer;width:100%;box-shadow:0 4px 12px rgba(161,136,127,0.3);transition:all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 16px rgba(161,136,127,0.4)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 12px rgba(161,136,127,0.3)'">OK, Got it!</button>
    </div>
</div>

<style>
@keyframes modalBounce {
    0% { transform: scale(0.7); opacity: 0; }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); opacity: 1; }
}
@keyframes checkBounce {
    0%, 100% { transform: scale(1); }
    25% { transform: scale(1.2) rotate(-5deg); }
    75% { transform: scale(1.2) rotate(5deg); }
}
</style>

<!-- Feeding Animation Overlay -->
<div id="feeding-overlay">
    <div class="feed-modal">
        <!-- Bowl SVG with food fill -->
        <div class="bowl-wrap">
            <!-- Falling pellets -->
            <div class="pellets">
                <div class="pellet"></div>
                <div class="pellet"></div>
                <div class="pellet"></div>
                <div class="pellet"></div>
                <div class="pellet"></div>
            </div>
            <svg class="bowl-svg" viewBox="0 0 160 160" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <clipPath id="bowl-clip">
                        <!-- Bowl shape clip: ellipse bottom + rect sides -->
                        <path d="M20,50 Q20,130 80,135 Q140,130 140,50 Z" />
                    </clipPath>
                </defs>
                <!-- Bowl outline -->
                <path d="M20,50 Q20,130 80,135 Q140,130 140,50" fill="none" stroke="#a1887f" stroke-width="4" stroke-linecap="round"/>
                <!-- Bowl rim -->
                <ellipse cx="80" cy="50" rx="60" ry="12" fill="none" stroke="#a1887f" stroke-width="4"/>
                <!-- Food fill (clipped to bowl shape) -->
                <rect id="bowl-food-fill" x="21" y="120" width="118" height="0"
                    fill="url(#food-grad)" clip-path="url(#bowl-clip)"/>
                <!-- Food surface wave -->
                <path id="food-wave" d="" fill="url(#food-grad)" clip-path="url(#bowl-clip)" opacity="0.6"/>
                <defs>
                    <linearGradient id="food-grad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#c8a882"/>
                        <stop offset="100%" stop-color="#8d6e63"/>
                    </linearGradient>
                </defs>
                <!-- Bowl shine -->
                <ellipse cx="55" cy="70" rx="8" ry="18" fill="rgba(255,255,255,0.18)" transform="rotate(-20,55,70)"/>
            </svg>
        </div>

        <div class="feed-title"><span class="status-dot"></span>Dispensing Feed</div>
        <div class="feed-cage" id="feed-cage-label">Cage �</div>

        <!-- Progress bar -->
        <div class="progress-bar-wrap">
            <div class="progress-bar-fill" id="feed-progress-bar"></div>
        </div>

        <div class="pct-text" id="feed-pct">0%</div>
        <div class="feed-countdown" id="feed-countdown"></div>
        <div class="feed-timer-label">seconds remaining</div>
    </div>
</div>

<script>
(function () {
    let animationShowing = false;
    let checkingInterval = null;
    let scheduleCheckInterval = null;
    let lastCheckedActive = false;
    let animationTimeout = null;
    let safetyTimeout = null;
    let displayTimer = null;
    let currentCage = 1;

    let animStartTime = null;   // actual animation/servo start time (ms)
    let animTotal = 1;          // feeding duration (seconds)

    // IMPORTANT:
    // True only after the countdown reaches 0.
    // This prevents "Done Feeding!" from appearing too early.
    let countdownFinished = false;

    const BOWL_EMPTY_Y = 120;
    const BOWL_MAX_H   = 68;

    function resetManualFeedButton() {
        manualFeedInFlight = false;

        const button = document.querySelector(
            '#manualFeedForm button[type="submit"]'
        );

        if (button) {
            button.disabled = false;
        }
    }

    function setBowlFill(pct) {
        const fill = document.getElementById('bowl-food-fill');

        if (!fill) return;

        const safePct = Math.max(0, Math.min(100, pct));
        const h = (safePct / 100) * BOWL_MAX_H;

        fill.setAttribute('y', BOWL_EMPTY_Y - h);
        fill.setAttribute('height', h);
    }

    // ============================================================
    // START FEEDING ANIMATION
    // ============================================================
    function runAnimation(duration, cage, startedAt) {

        if (animationShowing) {
            console.log(
                '[Animation] Already showing - duplicate animation ignored'
            );
            return;
        }

        animationShowing = true;

        // NEW FEEDING CYCLE
        // Reset this BEFORE the countdown starts.
        countdownFinished = false;

        currentCage = cage || 1;

        // Allow exactly ONE Done Feeding popup for this feeding cycle.
        window.__doneFeedingPopupShown = false;

        if (safetyTimeout) {
            clearTimeout(safetyTimeout);
            safetyTimeout = null;
        }

        if (animationTimeout) {
            clearTimeout(animationTimeout);
            animationTimeout = null;
        }

        const overlay = document.getElementById('feeding-overlay');
        const countdown = document.getElementById('feed-countdown');
        const progressBar = document.getElementById('feed-progress-bar');
        const pctText = document.getElementById('feed-pct');
        const cageLabel = document.getElementById('feed-cage-label');

        if (!overlay) {
            console.warn('[Animation] feeding-overlay not found');
            animationShowing = false;
            resetManualFeedButton();
            return;
        }

        overlay.classList.add('show');

        if (cageLabel) {
            cageLabel.textContent = 'Cage ' + (cage || '-');
        }

        animTotal = Math.max(
            1,
            parseInt(duration, 10) || 1
        );

        // If actual servo start time is available, use it.
        // Otherwise start immediately when Feed Now is clicked.
        animStartTime = startedAt
            ? new Date(startedAt).getTime()
            : Date.now();

        if (isNaN(animStartTime)) {
            animStartTime = Date.now();
        }

        // Reset visual state at the beginning.
        setBowlFill(0);

        if (progressBar) {
            progressBar.style.width = '0%';
        }

        if (pctText) {
            pctText.textContent = '0%';
        }

        if (countdown) {
            countdown.textContent = animTotal;
        }

        console.log(
            '[Animation] Started - duration: ' +
            animTotal +
            ' seconds' +
            (
                startedAt
                    ? ' (synced to servo start)'
                    : ' (instant - waiting for servo sync)'
            )
        );

        startDisplayTimer();
    }

    // ============================================================
    // DISPLAY / COUNTDOWN TIMER
    // ============================================================
    function startDisplayTimer() {

        if (displayTimer) {
            clearInterval(displayTimer);
        }

        const progressBar =
            document.getElementById('feed-progress-bar');

        const pctText =
            document.getElementById('feed-pct');

        const countdown =
            document.getElementById('feed-countdown');

        displayTimer = setInterval(function () {

            if (!animationShowing) {
                clearInterval(displayTimer);
                displayTimer = null;
                return;
            }

            const elapsed =
                (Date.now() - animStartTime) / 1000;

            const remaining =
                Math.max(0, animTotal - elapsed);

            const pct =
                Math.min(
                    100,
                    Math.round((elapsed / animTotal) * 100)
                );

            setBowlFill(pct);

            if (progressBar) {
                progressBar.style.width = pct + '%';
            }

            if (pctText) {
                pctText.textContent = pct + '%';
            }

            if (countdown) {
                countdown.textContent =
                    Math.ceil(remaining);
            }

            // ====================================================
            // COUNTDOWN FINISHED
            // ====================================================
            if (elapsed >= animTotal) {

                clearInterval(displayTimer);
                displayTimer = null;

                markDurationComplete();
            }

        }, 100);
    }

    // ============================================================
    // COUNTDOWN COMPLETE
    // ============================================================
    function markDurationComplete() {

        // VERY IMPORTANT:
        // The countdown is now officially finished.
        countdownFinished = true;

        setBowlFill(100);

        const progressBar =
            document.getElementById('feed-progress-bar');

        const pctText =
            document.getElementById('feed-pct');

        const countdown =
            document.getElementById('feed-countdown');

        if (progressBar) {
            progressBar.style.width = '100%';
        }

        if (pctText) {
            pctText.textContent = '100%';
        }

        if (countdown) {
            countdown.textContent = '0';
        }

        console.log(
            '[Animation] Countdown finished - 100%'
        );

        console.log(
            '[Animation] Waiting for servo completion before showing Done Feeding'
        );

        // ========================================================
        // SAFETY NET
        // ========================================================
        // If the servo never reports completion, prevent the
        // animation from staying forever.
        //
        // This does NOT happen during the normal countdown.
        // It only starts AFTER countdownFinished = true.
        // ========================================================
        if (!safetyTimeout) {

            safetyTimeout = setTimeout(function () {

                if (animationShowing) {

                    console.log(
                        '[Animation] Safety timeout - servo close was not detected'
                    );

                    hideAnimation();

                }

            }, 15000);
        }
    }

    // ============================================================
    // SYNC TO ACTUAL SERVO START
    // ============================================================
    function syncAnimationStart(startedAt, duration) {

        if (!animationShowing || !startedAt) {
            return;
        }

        const servoStart =
            new Date(startedAt).getTime();

        if (isNaN(servoStart)) {
            return;
        }

        // No meaningful difference.
        if (
            Math.abs(
                servoStart - animStartTime
            ) < 500
        ) {
            return;
        }

        animStartTime = servoStart;

        if (duration) {
            animTotal = Math.max(
                1,
                parseInt(duration, 10) || animTotal
            );
        }

        // IMPORTANT:
        // If timer has already finished before sync happens,
        // restart it so the countdown follows the actual servo
        // start time.
        if (!displayTimer && !countdownFinished) {
            startDisplayTimer();
        }

        console.log(
            '[Animation] Timer re-synced to actual servo start'
        );
    }

    // ============================================================
    // HIDE ANIMATION / SHOW DONE FEEDING
    // ============================================================
    function hideAnimation() {

        if (!animationShowing) {
            return;
        }

        // ========================================================
        // CRITICAL PROTECTION
        // ========================================================
        // If the backend/polling reports inactive too early,
        // DO NOT hide the animation and DO NOT show Done Feeding.
        //
        // Wait until the countdown reaches 0 first.
        // ========================================================
        if (!countdownFinished) {

            console.log(
                '[Animation] Servo inactive detected, BUT countdown is still running.'
            );

            console.log(
                '[Animation] Keeping animation visible. Done Feeding will NOT show yet.'
            );

            return;
        }

        // ========================================================
        // CLEANUP
        // ========================================================
        if (animationTimeout) {
            clearTimeout(animationTimeout);
            animationTimeout = null;
        }

        if (safetyTimeout) {
            clearTimeout(safetyTimeout);
            safetyTimeout = null;
        }

        if (displayTimer) {
            clearInterval(displayTimer);
            displayTimer = null;
        }

        const overlay =
            document.getElementById('feeding-overlay');

        const progressBar =
            document.getElementById('feed-progress-bar');

        const pctText =
            document.getElementById('feed-pct');

        if (overlay) {
            overlay.classList.remove('show');
        }

        // Reset visual state for next feeding.
        setBowlFill(0);

        if (progressBar) {
            progressBar.style.width = '0%';
        }

        if (pctText) {
            pctText.textContent = '0%';
        }

        animationShowing = false;
        lastCheckedActive = false;

        resetManualFeedButton();

        console.log(
            '[Animation] Hidden - countdown completed and servo closed'
        );

        // ========================================================
        // ONLY NOW SHOW DONE FEEDING
        // ========================================================
        showDoneFeedingModal(currentCage);
    }

    // ============================================================
    // CONTINUOUS FEEDING CHECK
    // ============================================================
    function startContinuousCheck() {

        if (checkingInterval) {
            return;
        }

        console.log(
            '[Feeder] Starting continuous check every 300ms for feeding animation...'
        );

        checkingInterval = setInterval(function () {

            fetch('/api/feeder/animation')

                .then(r => r.json())

                .then(function (data) {

                    if (data.active) {

                        // ==================================================
                        // SERVO IS ACTIVE
                        // ==================================================
                        if (animationShowing) {

                            // Feed Now may have started the animation
                            // before the backend detected the servo.
                            // Re-sync it to actual servo start.
                            syncAnimationStart(
                                data.started_at,
                                data.duration
                            );

                            lastCheckedActive = true;

                        } else if (!lastCheckedActive) {

                            // ==================================================
                            // SERVO OPENED - START ANIMATION
                            // ==================================================
                            console.log(
                                '[Feeder] Servo opened - starting animation NOW!'
                            );

                            const duration =
                                data.duration || 5;

                            const cage =
                                data.cage || 1;

                            const startedAt =
                                data.started_at || null;

                            if (
                                typeof notificationSystem !==
                                'undefined'
                            ) {

                                const feedType =
                                    data.type === 'scheduled'
                                        ? 'Scheduled'
                                        : 'Manual';

                                notificationSystem.feeder(
                                    '🍲 FEEDING NOW — ' +
                                    feedType +
                                    ' feeding started for Cage ' +
                                    cage +
                                    ' (' +
                                    duration +
                                    ' sec)',
                                    '✅'
                                );
                            }

                            runAnimation(
                                duration,
                                cage,
                                startedAt
                            );

                            lastCheckedActive = true;
                        }

                    } else {

                        // ==================================================
                        // SERVO IS INACTIVE / CLOSED
                        // ==================================================
                        lastCheckedActive = false;

                        if (animationShowing) {

                            console.log(
                                '[Feeder] Servo inactive detected'
                            );

                            // hideAnimation() itself checks
                            // countdownFinished.
                            //
                            // Therefore, if the countdown is still
                            // running, nothing will happen.
                            hideAnimation();
                        }
                    }

                })

                .catch(function (err) {

                    // Silent fail so the animation remains running
                    // even if one polling request fails.
                    console.warn(
                        '[Feeder] Animation polling error:',
                        err
                    );
                });

        }, 300);
    }

   // ============================================================
// SCHEDULE CHECK
// ============================================================
function startScheduleCheck() {

    if (scheduleCheckInterval) {
        return;
    }

    scheduleCheckInterval = setInterval(function () {

        fetch('/feeder/schedules/auto-check')
            .then(r => r.json())
            .then(function (data) {

                if (data.count > 0 && Array.isArray(data.triggered)) {

                    console.log(
                        '[Feeder] Scheduled feeds triggered:',
                        data.triggered
                    );

                    data.triggered.forEach(function (schedule) {

                        const duration =
                            parseInt(schedule.amount, 10) || 5;

                        const cage =
                            parseInt(schedule.cage, 10) || 1;

                        console.log(
                            '[Feeder] Starting scheduled animation:',
                            duration,
                            'seconds, cage',
                            cage
                        );

                        // Use the SAME existing animation function
                        // already used by Manual Feed.
                        if (typeof window.showFeedingAnimation === 'function') {
                            window.showFeedingAnimation(
                                duration,
                                cage
                            );
                        } else {
                            console.error(
                                '[Feeder] showFeedingAnimation() is not available.'
                            );
                        }
                    });
                }

            })
            .catch(function (err) {

                console.error(
                    '[Feeder] Schedule check failed:',
                    err
                );

            });

    }, 2000);
}

    // ============================================================
    // FEEDING DONE NOTIFICATION
    // ============================================================
    function showFeedingDoneNotif() {

        const modal =
            document.getElementById(
                'feeding-notif-modal'
            );

        const timeEl =
            document.getElementById(
                'feeding-notif-time'
            );

        if (!modal) {
            return;
        }

        const now = new Date();

        const months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];

        const hours = now.getHours();

        const minutes =
            String(now.getMinutes()).padStart(2, '0');

        const ampm =
            hours >= 12 ? 'PM' : 'AM';

        const h =
            hours % 12 || 12;

        if (timeEl) {

            timeEl.textContent =
                h +
                ':' +
                minutes +
                ' ' +
                ampm +
                ' — ' +
                months[now.getMonth()] +
                ' ' +
                now.getDate() +
                ', ' +
                now.getFullYear();
        }

        modal.style.display = 'flex';
    }

    // ============================================================
    // CLOSE FEEDING NOTIFICATION
    // ============================================================
    window.closeFeedingNotif = function () {

        const modal =
            document.getElementById(
                'feeding-notif-modal'
            );

        if (modal) {
            modal.style.display = 'none';
        }

        // Reload to show updated feeding history.
        location.reload();
    };

    // ============================================================
    // SHOW FEEDING ANIMATION GLOBALLY
    // ============================================================
    window.showFeedingAnimation = function (
        duration,
        cage
    ) {

        console.log(
            '[Manual Feed] Starting animation:',
            duration,
            'seconds, cage',
            cage
        );

        runAnimation(
            duration,
            cage
        );
    };

    // ============================================================
    // INITIALIZE
    // ============================================================
    startContinuousCheck();
    startScheduleCheck();

    console.log(
        '[Feeder] Animation system initialized'
    );

})();
</script>
@endsection

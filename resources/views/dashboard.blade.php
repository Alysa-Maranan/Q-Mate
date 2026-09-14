@extends('layouts.app')

@section('content')
@include('components.sidebar')

<style>
    /* Dashboard Style - Matching Learn Book Design */
    body {
        background: linear-gradient(135deg, #f5f0eb 0%, #efe8e4 50%, #e8dcd6 100%);
        min-height: 100vh;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    * {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    /* Sidebar content wrapper - Matching settings page behavior */
    .sidebar-content-wrap {
        margin-left: 300px;
        transition: margin-left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        padding-top: 0;
    }
    .sidebar-content-wrap.expanded {
        margin-left: 0;
        padding-top: 80px;
    }

    /* Container and content styling - matching Settings page */
    .container {
        max-width: 100%;
        margin: 0 auto;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    /* Expanded state - Full width content - matching Settings page */
    .sidebar-content-wrap.expanded .container {
        max-width: 1600px;
        margin: 0 auto;
        padding-left: 2rem;
        padding-right: 2rem;
    }

    /* Status cards grid - More columns when expanded */
    .status-cards-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        transition: grid-template-columns 0.4s ease;
    }

    /* Quail stats grid - More columns when expanded */
    .quail-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        transition: grid-template-columns 0.4s ease;
    }

    /* Expanded state - Wider grids */
    .sidebar-content-wrap.expanded .status-cards-grid {
        grid-template-columns: repeat(4, 1fr);
    }

    .sidebar-content-wrap.expanded .quail-stats-grid {
        grid-template-columns: repeat(5, 1fr);
    }

    /* Breed cards - fill the full section width.
       Single breed = one full-width card; dual breeds = side by side. */
    .breed-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    /* Order notification container */
    #orderNotificationsContainer {
        max-height: 500px;
        overflow-y: auto;
        transition: max-height 0.4s ease;
    }

    .sidebar-content-wrap.expanded #orderNotificationsContainer {
        max-height: 600px;
    }

    .order-notif-container {
        max-height: 500px;
        overflow-y: auto;
        transition: max-height 0.4s ease;
    }

    /* Responsive adjustments */
    @media (max-width: 1200px) {
        .status-cards-grid,
        .quail-stats-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        .sidebar-content-wrap.expanded .status-cards-grid,
        .sidebar-content-wrap.expanded .quail-stats-grid {
            grid-template-columns: repeat(3, 1fr) !important;
        }
    }

    @media (max-width: 768px) {
        .sidebar-content-wrap {
            margin-left: 0 !important;
            padding-top: 70px;
        }
        .container { padding: 1.5rem 1rem; }
        .status-cards-grid,
        .quail-stats-grid,
        .breed-cards-grid {
            grid-template-columns: 1fr !important;
        }
        .sidebar-content-wrap.expanded .status-cards-grid,
        .sidebar-content-wrap.expanded .quail-stats-grid,
        .sidebar-content-wrap.expanded .breed-cards-grid {
            grid-template-columns: 1fr !important;
        }
    }

</style>
<div id="main-content-wrap" class="sidebar-content-wrap">
<div class="container" style="padding: 2rem;">
<!-- Header -->
    <div style="background: white; border-radius: 24px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 4px 20px rgba(109,76,65,0.08); border: 1px solid rgba(161, 136, 127, 0.1); text-align: center;">
        <h1 style="font-size: 2rem; font-weight: 800; color: #6d4c41; margin: 0;">Dashboard</h1>
        <p style="margin: 1rem 0 0 0; color: #4e342e; font-size: 1.05rem; line-height: 1.7; font-weight: 500; letter-spacing: 0.2px;">Welcome, {{ auth()->user()->name ?? 'User' }}</p>
        <p style="margin: 1.5rem 0 0 0; color: #8d6e63; font-size: 0.95rem; line-height: 1.8; font-style: italic; border-top: 1px solid #efebe9; padding-top: 1.5rem;">"Start small, dream big, and work hard for your success."</p>
    </div>

    <!-- Current Breed Info -->
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
    <div style="background: white; padding: 2rem; border-radius: 20px; margin-bottom: 2rem; box-shadow: 0 4px 20px rgba(109,76,65,0.08);">
        <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid #d7ccc8; flex-direction: column; text-align: center;">
            <div>
                <h3 style="margin: 0 0 0.5rem 0; color: #6d4c41; font-weight: 700; font-size: 1.25rem;">Current Quail Breeds</h3>
                <p style="margin: 0; color: #8d6e63; font-size: 0.95rem;">{{ count($uniqueBreeds) == 1 ? 'Single breed' : 'Dual breed farming system' }}</p>
            </div>
        </div>
        <div class="breed-cards-grid">
            @foreach($uniqueBreeds as $index => $breed)
            <div style="background: linear-gradient(135deg, #fffdfa 0%, #f5eee6 100%); padding: 1.5rem; border-radius: 12px; border-left: 4px solid #a1887f;">
                <p style="margin: 0 0 0.5rem 0; color: #8d6e63; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Breed #{{ $index + 1 }}</p>
                <h4 style="margin: 0 0 0.5rem 0; color: #6d4c41; font-weight: 700; font-size: 1.1rem;">{{ $breed->name }}</h4>
                <p style="margin: 0 0 1rem 0; color: #8d6e63; font-size: 0.9rem;">{{ $breed->description }}</p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div style="background: white; padding: 0.75rem; border-radius: 8px; border-left: 3px solid #795548;">
                        <p style="margin: 0; color: #8d6e63; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Scientific Name</p>
                        <p style="margin: 0.25rem 0 0; color: #6d4c41; font-weight: 700; font-size: 0.9rem;">{{ $breed->scientific_name }}</p>
                    </div>
                    <div style="background: white; padding: 0.75rem; border-radius: 8px; border-left: 3px solid #795548;">
                        <p style="margin: 0; color: #8d6e63; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Egg Production</p>
                        <p style="margin: 0.25rem 0 0; color: #6d4c41; font-weight: 700; font-size: 0.9rem;">{{ $breed->egg_production_rate }}/year</p>
                    </div>
                    <div style="background: white; padding: 0.75rem; border-radius: 8px; border-left: 3px solid #795548;">
                        <p style="margin: 0; color: #8d6e63; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Mature Weight</p>
                        <p style="margin: 0.25rem 0 0; color: #6d4c41; font-weight: 700; font-size: 0.9rem;">{{ $breed->mature_weight }}g</p>
                    </div>
                    <div style="background: white; padding: 0.75rem; border-radius: 8px; border-left: 3px solid #795548;">
                        <p style="margin: 0; color: #8d6e63; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Maturity Age</p>
                        <p style="margin: 0.25rem 0 0; color: #6d4c41; font-weight: 700; font-size: 0.9rem;">{{ $breed->maturity_age }} days</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @endisset

    <!-- Status Cards Section -->
    <div style="margin-bottom: 2rem;">
        <h2 style="color: #6d4c41; margin: 0 0 1.5rem 0; font-size: 1.25rem; font-weight: 700;">System Status</h2>
        <div class="status-cards-grid">
            
            <!-- Food Level Card -->
            <div style="background: white; padding: 1.5rem; border-radius: 16px; border: 2px solid #d7ccc8; box-shadow: 0 4px 20px rgba(109,76,65,0.08); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 40px rgba(161, 136, 127, 0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 32px rgba(161,136,127,0.12)';">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div style="width: 48px; height: 48px; background: #efebe9; border-radius: 12px; display: flex; align-items: center; justify-content: center; border-left: 4px solid #a1887f;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#795548" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M8 12h8m-8 6h8" />
                        </svg>
                    </div>
                    <div style="flex: 1;">
                        <p style="margin: 0; color: #8d6e63; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Food Level</p>
                        <p id="foodLevelValue" style="margin: 0.5rem 0 0; color: #6d4c41; font-weight: 700; font-size: 1.1rem;">Loading...</p>
                    </div>
                </div>
                <div style="background: #efebe9; border-radius: 8px; height: 8px; overflow: hidden;">
                    <div id="foodLevelBar" style="height: 100%; width: 0%; background: #4caf50; border-radius: 8px; transition: width 0.5s ease, background 0.5s ease;"></div>
                </div>
                <p id="foodLevelStatus" style="margin: 0.5rem 0 0; color: #8d6e63; font-size: 0.8rem;"></p>
            </div>

            <!-- Temperature & Humidity Card -->
            <div style="background: white; padding: 1.5rem; border-radius: 16px; border: 2px solid #d7ccc8; box-shadow: 0 4px 20px rgba(109,76,65,0.08); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 40px rgba(161, 136, 127, 0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 32px rgba(161,136,127,0.12)';">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div style="width: 48px; height: 48px; background: #efebe9; border-radius: 12px; display: flex; align-items: center; justify-content: center; border-left: 4px solid #a1887f;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#795548" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 4v10.54a4 4 0 1 1-4 0V4a2 2 0 0 1 4 0z" />
                        </svg>
                    </div>
                    <div style="flex: 1;">
                        <p style="margin: 0; color: #8d6e63; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Temperature & Humidity</p>
                        <p id="tempValue" style="margin: 0.5rem 0 0; color: #6d4c41; font-weight: 700; font-size: 1.1rem;">{{ ($latestReading && !is_null($latestReading->temperature)) ? number_format($latestReading->temperature, 1) . '°C' : '--°C' }}</p>
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="margin: 0; color: #8d6e63; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Humidity</p>
                        <p id="humidityValue" style="margin: 0.15rem 0 0; color: #6d4c41; font-weight: 700; font-size: 1rem;">{{ ($latestReading && !is_null($latestReading->humidity)) ? round($latestReading->humidity, 1) . '%' : '--%' }}</p>
                    </div>
                    <a href="{{ route('temperature.humidity') }}" style="display: inline-block; color: #a1887f; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: color 0.2s;" onmouseover="this.style.color='#795548';" onmouseout="this.style.color='#a1887f';">View Details →</a>
                </div>
                <p id="sensorUpdatedAt" style="margin: 0.5rem 0 0; color: #a1887f; font-size: 0.75rem;">{{ ($latestReading && $latestReading->recorded_at) ? 'Updated ' . $latestReading->recorded_at->format('M d, g:i A') : 'No recent sensor data' }}</p>
            </div>

            <!-- Inventory Card -->
            <div style="background: white; padding: 1.5rem; border-radius: 16px; border: 2px solid #d7ccc8; box-shadow: 0 4px 20px rgba(109,76,65,0.08); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 40px rgba(161, 136, 127, 0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 32px rgba(161,136,127,0.12)';">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div style="width: 48px; height: 48px; background: #efebe9; border-radius: 12px; display: flex; align-items: center; justify-content: center; border-left: 4px solid #a1887f;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#795548" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="m3.3 7 8.7 5 8.7-5" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 22V12" />
                        </svg>
                    </div>
                    <div style="flex: 1;">
                        <p style="margin: 0; color: #8d6e63; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Inventory</p>
                        @php
                            $invColor = $inventoryStatus['level'] === 'out' ? '#d32f2f' : ($inventoryStatus['level'] === 'low' ? '#ff9800' : '#6d4c41');
                        @endphp
                        <p id="inventoryStatusValue" style="margin: 0.5rem 0 0; color: {{ $invColor }}; font-weight: 700; font-size: 1.1rem;">{{ $inventoryStatus['label'] }}</p>
                    </div>
                </div>
                <p id="inventoryStatusDetail" style="margin: 0 0 0.5rem 0; color: #8d6e63; font-size: 0.78rem; line-height: 1.4;">{{ $inventoryStatus['detail'] }}</p>
                <a href="{{ route('inventory') }}" style="display: inline-block; color: #a1887f; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: color 0.2s;" onmouseover="this.style.color='#795548';" onmouseout="this.style.color='#a1887f';">View Details →</a>
            </div>

            <!-- Feeding Activity Card -->
            <div style="background: white; padding: 1.5rem; border-radius: 16px; border: 2px solid #d7ccc8; box-shadow: 0 4px 20px rgba(109,76,65,0.08); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 40px rgba(161, 136, 127, 0.15)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 32px rgba(161,136,127,0.12)';">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div style="width: 48px; height: 48px; background: #efebe9; border-radius: 12px; display: flex; align-items: center; justify-content: center; border-left: 4px solid #a1887f;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#795548" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 11h18a9 9 0 0 1-18 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 8V6m4 2V5m4 3V6" />
                        </svg>
                    </div>
                    <div style="flex: 1;">
                        <p style="margin: 0; color: #8d6e63; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Feeding Activity</p>
                        <p id="feedsTodayValue" style="margin: 0.5rem 0 0; color: #6d4c41; font-weight: 700; font-size: 1.1rem;">{{ $feedInfo['feeds_today'] }} feed(s) today</p>
                    </div>
                </div>
                <p id="lastFedValue" style="margin: 0 0 0.5rem 0; color: #8d6e63; font-size: 0.78rem;">Last fed: {{ $feedInfo['last_fed_at'] ?? 'No record yet' }}</p>
                <a href="{{ route('feeder') }}" style="display: inline-block; color: #a1887f; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: color 0.2s;" onmouseover="this.style.color='#795548';" onmouseout="this.style.color='#a1887f';">Open Feeder →</a>
            </div>
            </div>
    </div>

    <!-- Today's Summary -->
    <div style="margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid #d7ccc8;">
            <h2 style="color: #6d4c41; margin: 0; font-size: 1.25rem; font-weight: 700;">Today's Summary</h2>
            {{-- DEBUG FARM KEYS (uncomment if needed)
            <pre style="margin:0;background:#fff;color:#333;font-size:12px;max-height:120px;overflow:auto;">{{ print_r($farm, true) }}</pre>
            --}}
        </div>
        <div class="quail-stats-grid">
            
            <div style="background: #efebe9; border-radius: 16px; padding: 1.5rem; border: 2px solid #d7ccc8; border-left: 4px solid #4caf50; transition: all 0.3s ease;">
                <div style="font-size: 0.9rem; font-weight: 700; color: #6d4c41; margin-bottom: 0.5rem; text-transform: uppercase;">Total Eggs Collected</div>
                <div id="totalEggsToday" style="color: #795548; font-size: 1.8rem; font-weight: bold;">{{ $farm['total_eggs_collected'] ?? ($farm['total_eggs'] ?? 0) }} eggs</div>
                {{-- DEBUG: Uncomment to inspect available farm keys --}}
                {{-- <pre style="text-align:left;overflow:auto;max-height:200px;">{{ print_r($farm ?? null, true) }}</pre> --}}
                <p style="margin: 0.75rem 0 0 0; color: #8d6e63; font-size: 0.85rem;">Today</p>
            </div>

            <div style="background: #e8f5e9; border-radius: 16px; padding: 1.5rem; border: 2px solid #d7ccc8; border-left: 4px solid #4caf50; transition: all 0.3s ease;">
                <div style="font-size: 0.9rem; font-weight: 700; color: #6d4c41; margin-bottom: 0.5rem; text-transform: uppercase;">Number of good eggs</div>
                <div id="goodEggsToday" style="color: #2e7d32; font-size: 1.8rem; font-weight: bold;">{{ $farm['good_eggs'] ?? $farm['total_good_eggs'] ?? 0 }} eggs</div>
                <p style="margin: 0.75rem 0 0 0; color: #8d6e63; font-size: 0.85rem;">Today</p>
            </div>

            <div style="background: #ffebee; border-radius: 16px; padding: 1.5rem; border: 2px solid #d7ccc8; border-left: 4px solid #d32f2f; transition: all 0.3s ease;">
                <div style="font-size: 0.9rem; font-weight: 700; color: #6d4c41; margin-bottom: 0.5rem; text-transform: uppercase;">Cracked/Damaged Eggs</div>
                <div id="crackedEggsToday" style="color: #c62828; font-size: 1.8rem; font-weight: bold;">{{ $farm['cracked_eggs'] ?? 0 }} eggs</div>
                <p style="margin: 0.75rem 0 0 0; color: #8d6e63; font-size: 0.85rem;">Today</p>
            </div>
        </div>
    </div>

    <!-- Sales & Orders Overview -->
    <div style="margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid #d7ccc8;">
            <h2 style="color: #6d4c41; margin: 0; font-size: 1.25rem; font-weight: 700;">Sales & Orders</h2>
            <a href="{{ route('orders-products') }}" style="color: #a1887f; text-decoration: none; font-weight: 600; font-size: 0.9rem;">Manage Orders →</a>
        </div>
        <div class="quail-stats-grid">

            <div style="background: #e8f5e9; border-radius: 16px; padding: 1.5rem; border: 2px solid #d7ccc8; border-left: 4px solid #2e7d32; transition: all 0.3s ease;">
                <div style="font-size: 0.9rem; font-weight: 700; color: #6d4c41; margin-bottom: 0.5rem; text-transform: uppercase;">Sales Today</div>
                <div id="salesTodayValue" style="color: #2e7d32; font-size: 1.8rem; font-weight: bold;">₱{{ number_format($salesSummary['today_revenue'], 2) }}</div>
                <p id="salesTodayCount" style="margin: 0.75rem 0 0 0; color: #8d6e63; font-size: 0.85rem;">{{ $salesSummary['today_count'] }} sale(s)</p>
            </div>

            <div style="background: #efebe9; border-radius: 16px; padding: 1.5rem; border: 2px solid #d7ccc8; border-left: 4px solid #a1887f; transition: all 0.3s ease;">
                <div style="font-size: 0.9rem; font-weight: 700; color: #6d4c41; margin-bottom: 0.5rem; text-transform: uppercase;">Orders Today</div>
                <div id="ordersTodayValue" style="color: #795548; font-size: 1.8rem; font-weight: bold;">{{ $ordersSummary['today'] }}</div>
                <p style="margin: 0.75rem 0 0 0; color: #8d6e63; font-size: 0.85rem;">New orders received today</p>
            </div>

            <div style="background: #fff3e0; border-radius: 16px; padding: 1.5rem; border: 2px solid #d7ccc8; border-left: 4px solid #ff9800; transition: all 0.3s ease;">
                <div style="font-size: 0.9rem; font-weight: 700; color: #6d4c41; margin-bottom: 0.5rem; text-transform: uppercase;">Pending Orders</div>
                <div id="pendingOrdersValue" style="color: #ef6c00; font-size: 1.8rem; font-weight: bold;">{{ $ordersSummary['pending'] }}</div>
                <p style="margin: 0.75rem 0 0 0; color: #8d6e63; font-size: 0.85rem;">Awaiting confirmation</p>
            </div>

            <div style="background: #efebe9; border-radius: 16px; padding: 1.5rem; border: 2px solid #d7ccc8; border-left: 4px solid #2196f3; transition: all 0.3s ease;">
                <div style="font-size: 0.9rem; font-weight: 700; color: #6d4c41; margin-bottom: 0.5rem; text-transform: uppercase;">Total Revenue</div>
                <div id="totalRevenueValue" style="color: #1565c0; font-size: 1.8rem; font-weight: bold;">₱{{ number_format($salesSummary['total_revenue'], 2) }}</div>
                <p id="totalSalesCount" style="margin: 0.75rem 0 0 0; color: #8d6e63; font-size: 0.85rem;">{{ $salesSummary['total_count'] }} total sale(s)</p>
            </div>
        </div>
        @php
            $statusChips = [
                'confirmed' => ['Confirmed', '#e3f2fd', '#1976d2'],
                'processing' => ['Processing', '#ede7f6', '#7b1fa2'],
                'to_ship' => ['To Ship', '#e8f5e9', '#2e7d32'],
                'completed' => ['Completed', '#efebe9', '#6d4c41'],
                'cancelled' => ['Cancelled', '#ffebee', '#c62828'],
            ];
        @endphp
        <div style="margin-top: 1rem; display: flex; flex-wrap: wrap; gap: 0.5rem;" id="orderStatusChips">
            @foreach ($statusChips as $statusKey => [$chipLabel, $chipBg, $chipColor])
            <span data-status-count="{{ $statusKey }}" style="display: inline-block; padding: 0.35rem 0.85rem; background: {{ $chipBg }}; color: {{ $chipColor }}; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">{{ $chipLabel }}: {{ $ordersSummary[$statusKey] }}</span>
            @endforeach
        </div>
    </div>

    <!-- Quail Farm Statistics -->
    <div style="margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid #d7ccc8;">
            <h2 style="color: #6d4c41; margin: 0; font-size: 1.25rem; font-weight: 700;">Quail Farm Statistics</h2>
            <div style="color: #8d6e63; font-size: 0.9rem; font-weight: 600;">
                <span style="text-transform: uppercase; letter-spacing: 0.5px;">Last Updated:</span> {{ isset($farm['quail_count_date']) ? date('M d, Y', strtotime($farm['quail_count_date'])) : 'No data yet' }}
            </div>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem;">
            
            <div style="background: #efebe9; border-radius: 16px; padding: 1.5rem; border: 2px solid #d7ccc8; border-left: 4px solid #4caf50; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(161, 136, 127, 0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                <div style="font-size: 1rem; font-weight: 700; color: #6d4c41; margin-bottom: 0.5rem;">Live Quails</div>
                <div style="color: #795548; font-size: 1.8rem; font-weight: bold;">{{ $farm['live_quails'] ?? 0 }}</div>
            </div>

            <div style="background: #efebe9; border-radius: 16px; padding: 1.5rem; border: 2px solid #d7ccc8; border-left: 4px solid #d32f2f; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(161, 136, 127, 0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                <div style="font-size: 1rem; font-weight: 700; color: #6d4c41; margin-bottom: 0.5rem;">Dead Quails</div>
                <div style="color: #795548; font-size: 1.8rem; font-weight: bold;">{{ $farm['dead_quails'] ?? 0 }}</div>
            </div>

            <div style="background: #efebe9; border-radius: 16px; padding: 1.5rem; border: 2px solid #d7ccc8; border-left: 4px solid #2196f3; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(161, 136, 127, 0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                <div style="font-size: 1rem; font-weight: 700; color: #6d4c41; margin-bottom: 0.5rem;">Total Raised</div>
                <div style="color: #795548; font-size: 1.8rem; font-weight: bold;">{{ ($farm['live_quails'] ?? 0) + ($farm['dead_quails'] ?? 0) }}</div>
            </div>

            <div style="background: #efebe9; border-radius: 16px; padding: 1.5rem; border: 2px solid #d7ccc8; border-left: 4px solid #ff9800; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(161, 136, 127, 0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                <div style="font-size: 1rem; font-weight: 700; color: #6d4c41; margin-bottom: 0.5rem;">Mortality Rate</div>
                <div style="color: #795548; font-size: 1.8rem; font-weight: bold;">
                    @php
                        $liveQuails = $farm['live_quails'] ?? 0;
                        $deadQuails = $farm['dead_quails'] ?? 0;
                        $totalQuails = $liveQuails + $deadQuails;
                        $mortalityRate = $totalQuails > 0 ? round(($deadQuails / $totalQuails) * 100, 2) : 0;
                    @endphp
                    {{ $mortalityRate }}%
                </div>
            </div>
        </div>
        <div style="margin-top: 1rem; text-align: right;">
            <a href="{{ route('inventory') }}?tab=quail-management" style="color: #a1887f; text-decoration: none; font-weight: 600; font-size: 0.9rem;">Update Inventory →</a>
        </div>
    </div>

    <!-- Egg Production Trend (Last 7 Days) -->
    <div style="margin-bottom: 2rem; background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 4px 20px rgba(109,76,65,0.08); border: 1px solid rgba(161, 136, 127, 0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid #d7ccc8;">
            <h2 style="color: #6d4c41; margin: 0; font-size: 1.25rem; font-weight: 700;">Egg Production Trend</h2>
            <span style="color: #8d6e63; font-size: 0.85rem; font-weight: 600;">Last 7 Days • Total vs Good Eggs</span>
        </div>
        <canvas id="eggTrendChart" height="110"></canvas>
    </div>

    <!-- Notifications -->
    <div style="margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="color: #6d4c41; margin: 0; font-size: 1.25rem; font-weight: 700;">Notification</h2>
            <button onclick="clearAllOrderNotifications()" style="background: #efebe9; border: none; padding: 0.5rem 1rem; border-radius: 8px; color: #6d4c41; font-weight: 600; cursor: pointer; font-size: 0.85rem; transition: all 0.2s;" onmouseover="this.style.background='#d7ccc8';" onmouseout="this.style.background='#efebe9';">Clear All</button>
        </div>
        <div style="background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 4px 20px rgba(109,76,65,0.08); border: 1px solid rgba(161, 136, 127, 0.1);">
            <div id="orderNotificationsContainer" class="order-notif-container">
                <!-- Notifications will be populated here by JavaScript -->
                <div class="notification-empty" style="text-align: center; color: #a1887f; padding: 2rem;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="none" viewBox="0 0 24 24" stroke="#a1887f" stroke-width="1.5" style="display:inline-block;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <p style="margin: 0; color: #6d4c41; font-size: 1.1rem; font-weight: 600;">No Notifications Yet</p>
                    <p style="margin: 0.5rem 0 0 0; color: #a1887f; font-size: 0.9rem;">New notifications will appear here</p>
                </div>
            </div>
        </div>

        <style>
            #orderNotificationsContainer::-webkit-scrollbar {
                width: 6px;
            }
            #orderNotificationsContainer::-webkit-scrollbar-track {
                background: #f5f0eb;
                border-radius: 10px;
            }
            #orderNotificationsContainer::-webkit-scrollbar-thumb {
                background: #d7ccc8;
                border-radius: 10px;
            }
            #orderNotificationsContainer::-webkit-scrollbar-thumb:hover {
                background: #a1887f;
            }
            .order-notif-item {
                padding: 0.75rem;
                border-bottom: 1px solid #efebe9;
                transition: background 0.2s ease;
            }
            .order-notif-item:hover {
                background: #faf7f3;
            }
            .order-notif-item:last-child {
                border-bottom: none;
            }
            .order-notif-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 0.5rem;
            }
            .order-notif-title {
                font-size: 0.9rem;
                font-weight: 700;
                color: #6d4c41;
                margin: 0;
            }
            .order-notif-time {
                font-size: 0.75rem;
                color: #a1887f;
                font-weight: 600;
            }
            .order-notif-details {
                background: #f5f0eb;
                padding: 0.5rem;
                border-radius: 8px;
                margin-bottom: 0.5rem;
            }
            .order-detail-row {
                display: flex;
                justify-content: space-between;
                padding: 0.25rem 0;
                border-bottom: 1px solid #efebe9;
            }
            .order-detail-row:last-child {
                border-bottom: none;
            }
            .order-detail-label {
                font-weight: 600;
                color: #8d6e63;
                font-size: 0.8rem;
            }
            .order-detail-value {
                color: #6d4c41;
                font-weight: 700;
                font-size: 0.8rem;
            }
            .order-badge {
                display: inline-block;
                padding: 0.25rem 0.5rem;
                background: #e8f5e9;
                border-radius: 8px;
                font-size: 0.7rem;
                font-weight: 700;
                color: #2e7d32;
            }
        </style>

        <script>
            // ORDER NOTIFICATION SYSTEM - DISPLAY ONLY
            let lastCheckedOrderId = 0; // Always start from 0 to show modal for new orders
            let lastCheckedReviewId = 0; // For review notifications
            let lastCheckedCancellationId = 0; // For cancellation notifications
            let lastCheckedChatId = 0; // For chat notifications (ContactMessage)
            let lastCheckedChatMsgId = 0; // For chat notifications (Chat model)

            // Load acknowledged items from localStorage
            function getAcknowledgedItems() {
                const acknowledged = localStorage.getItem('acknowledged_notifications');
                return acknowledged ? JSON.parse(acknowledged) : [];
            }

            function acknowledgeItem(type, id) {
                const acknowledged = getAcknowledgedItems();
                const key = type + '_' + id;
                if (!acknowledged.includes(key)) {
                    acknowledged.push(key);
                    localStorage.setItem('acknowledged_notifications', JSON.stringify(acknowledged));
                }
                // Persist acknowledgment to the database so it stays consistent across devices/browsers
                syncAcksToServer([key]);
            }

            function csrfToken() {
                const meta = document.querySelector('meta[name="csrf-token"]');
                return meta ? meta.getAttribute('content') : '';
            }

            function syncAcksToServer(keys) {
                if (!keys || !keys.length) return;
                fetch('/api/admin/notification-acks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ keys: keys })
                }).catch(() => {});
            }

            // Pull DB acknowledgments on load so popups stay hidden across devices/browsers
            function loadServerAcks() {
                fetch('/api/admin/notification-acks', { headers: { 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.keys) return;
                        const acknowledged = getAcknowledgedItems();
                        let changed = false;
                        data.keys.forEach(key => {
                            if (!acknowledged.includes(key)) {
                                acknowledged.push(key);
                                changed = true;
                            }
                        });
                        if (changed) {
                            localStorage.setItem('acknowledged_notifications', JSON.stringify(acknowledged));
                        }
                    })
                    .catch(() => {});
            }

            function isAcknowledged(type, id) {
                const acknowledged = getAcknowledgedItems();
                return acknowledged.includes(type + '_' + id);
            }

            function getStoredNotifications() {
                try {
                    const saved = localStorage.getItem('dashboard_notifications');
                    return saved ? JSON.parse(saved) : [];
                } catch (error) {
                    return [];
                }
            }

            function saveStoredNotifications(items) {
                try {
                    localStorage.setItem('dashboard_notifications', JSON.stringify(items));
                } catch (error) {
                    console.log('Unable to save dashboard notifications:', error);
                }
            }

            function mergeStoredNotifications(newItems) {
                const stored = getStoredNotifications();
                const map = new Map();

                [...stored, ...newItems].forEach(item => {
                    if (!item || !item.type || !item.id) return;
                    const key = `${item.type}_${item.id}`;
                    map.set(key, item);
                });

                return Array.from(map.values()).sort((a, b) => {
                    const aTime = new Date(a.date || a.created_at || 0).getTime();
                    const bTime = new Date(b.date || b.created_at || 0).getTime();
                    return bTime - aTime;
                });
            }

            function loadOrderNotifications() {
                // Use allSettled so a crash in ANY single endpoint cannot block the others.
                // Each endpoint is fetched independently; only the successful results feed the UI.
                const pOrdersPending    = fetch('/api/orders/pending', { credentials: 'same-origin' }).then(r => r.json()).catch(e => null);
                const pOrdersAll       = fetch('/api/orders/all', { credentials: 'same-origin' }).then(r => r.json()).catch(e => null);
                const pReviews         = fetch('/api/admin/reviews/all', { credentials: 'same-origin' }).then(r => r.json()).catch(e => null);
                const pContactChats    = fetch('/api/chats/recent', { credentials: 'same-origin' }).then(r => r.json()).catch(e => null);
                const pContactChatPnd  = fetch('/api/chats/pending', { credentials: 'same-origin' }).then(r => r.json()).catch(e => null);
                const pChatMessages    = fetch('/api/chat/messages/recent', { credentials: 'same-origin' }).then(r => r.json()).catch(e => null);
                const pChatMsgPnd      = fetch('/api/chat/messages/pending', { credentials: 'same-origin' }).then(r => r.json()).catch(e => null);

                Promise.allSettled([pOrdersPending, pOrdersAll, pReviews,
                                    pContactChats, pContactChatPnd,
                                    pChatMessages, pChatMsgPnd])
                .then(results => {
                    const orderData      = results[0].status === 'fulfilled' ? results[0].value : null;
                    const orders         = results[1].status === 'fulfilled' ? results[1].value : [];
                    const reviewsData    = results[2].status === 'fulfilled' ? results[2].value : null;
                    const contactChats   = results[3].status === 'fulfilled' ? results[3].value : [];
                    const contactChatData= results[4].status === 'fulfilled' ? results[4].value : null;
                    const chatMessages   = results[5].status === 'fulfilled' ? results[5].value : [];
                    const chatMessagesData = results[6].status === 'fulfilled' ? results[6].value : null;

                    const reviews = reviewsData && reviewsData.reviews ? reviewsData.reviews : [];

                    // Combine both chat sources (ContactMessage and Chat model)
                    // Deduplicate by message content + time (to avoid showing same message twice)
                    const seenChats = new Set();
                    const allChats = [...(contactChats || []), ...(chatMessages || [])].filter(chat => {
                        if (!chat || !chat.message || !chat.created_at) return false;
                        const key = chat.message + '|' + chat.created_at;
                        if (seenChats.has(key)) return false;
                        seenChats.add(key);
                        return true;
                    });

                    // First, display all notifications in the list
                    displayAllNotifications(orders, reviews, allChats);

                    // Then check for new orders and show popup (if not acknowledged)
                    if (orderData && orderData.lastId > lastCheckedOrderId) {
                        lastCheckedOrderId = orderData.lastId;
                        localStorage.setItem('last_order_check_id', lastCheckedOrderId.toString());
                        if (orderData.lastOrder && orderData.lastOrder.status !== 'cancelled') {
                            if (!isAcknowledged('order', orderData.lastOrder.id)) {
                                playOrderSound();
                                showOrderPopup(orderData.lastOrder);
                            }
                        }
                    }

                    // Check for newly cancelled orders (if not acknowledged)
                    if (orderData && orderData.lastCancellationId > lastCheckedCancellationId) {
                        lastCheckedCancellationId = orderData.lastCancellationId;
                        if (orderData.lastCancelledOrder && !isAcknowledged('cancellation', orderData.lastCancelledOrder.id)) {
                            playOrderSound();
                            showCancellationPopup(orderData.lastCancelledOrder);
                        }
                    }

                    // Check for new reviews (if not acknowledged)
                    if (reviews.length > 0 && reviews[0].id > lastCheckedReviewId) {
                        lastCheckedReviewId = reviews[0].id;
                        if (!isAcknowledged('review', reviews[0].id)) {
                            playOrderSound();
                            showReviewPopup(reviews[0]);
                        }
                    }

                    // Check for new ContactMessage chats (if not acknowledged)
                    if (contactChatData && contactChatData.lastId > lastCheckedChatId) {
                        lastCheckedChatId = contactChatData.lastId;
                        if (contactChatData.lastChat && !isAcknowledged('contact_chat', contactChatData.lastChat.id)) {
                            playOrderSound();
                            showChatPopup(contactChatData.lastChat);
                        }
                    }

                    // Check for new Chat model messages (if not acknowledged)
                    if (chatMessagesData && chatMessagesData.lastId > lastCheckedChatMsgId) {
                        lastCheckedChatMsgId = chatMessagesData.lastId;
                        if (chatMessagesData.lastChat && !isAcknowledged('chat_message', chatMessagesData.lastChat.id)) {
                            playOrderSound();
                            showChatMessagePopup(chatMessagesData.lastChat);
                        }
                    }
                })
                .catch(error => {
                    console.log('Error loading notifications:', error);
                });
            }

            let currentPopupReviewId = null;

            function showReviewPopup(review) {
                currentPopupReviewId = review.id;
                const stars = '★'.repeat(review.rating) + '☆'.repeat(5 - review.rating);
                document.getElementById('reviewPopupCustomer').textContent = review.customer_name;
                document.getElementById('reviewPopupProduct').textContent = review.product_name;
                document.getElementById('reviewPopupRating').textContent = stars;
                document.getElementById('reviewPopupComment').textContent = review.comment;
                document.getElementById('reviewPopupTime').textContent = review.time;

                document.getElementById('reviewPopupModal').style.display = 'flex';
            }

            function closeReviewPopup() {
                document.getElementById('reviewPopupModal').style.display = 'none';
                if (currentPopupReviewId) {
                    acknowledgeItem('review', currentPopupReviewId);
                    currentPopupReviewId = null;
                }
            }

            function displayAllNotifications(orders, reviews, chats) {
                const container = document.getElementById('orderNotificationsContainer');
                if (!container) return;

                const orderItems = orders.map(order => {
                    const orderDate = new Date(order.created_at);
                    const timeStr = orderDate.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                    const dateStr = orderDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    const isCancelled = order.type === 'cancelled_order' || order.status === 'cancelled';

                    if (isCancelled) {
                        return {
                            type: 'cancellation',
                            id: order.id,
                            time: timeStr,
                            date: dateStr,
                            customer: order.fullname || order.name || 'N/A',
                            product: order.product || 'N/A',
                            quantity: order.quantity || 'N/A',
                            contact: order.contact || order.phone || 'N/A',
                            location: order.location || order.address || 'N/A',
                            reason: order.cancellation_reason || 'N/A'
                        };
                    }

                    return {
                        type: 'order',
                        id: order.id,
                        time: timeStr,
                        date: dateStr,
                        customer: order.fullname || extractDetail(order.message, 'Name') || 'N/A',
                        product: order.product || extractDetail(order.message, 'Product') || 'N/A',
                        quantity: order.quantity || extractDetail(order.message, 'Qty') || 'N/A',
                        contact: order.contact || extractDetail(order.message, 'Contact') || 'N/A',
                        location: order.location || extractDetail(order.message, 'Location') || 'N/A'
                    };
                });

                // Process reviews
                const reviewItems = reviews.map(review => ({
                    type: 'review',
                    id: review.id,
                    time: review.time,
                    date: new Date(review.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
                    customer: review.customer_name,
                    product: review.product_name,
                    rating: review.rating,
                    comment: review.comment
                }));

                // Process chats
                const chatItems = (chats || []).map(chat => {
                    // Handle Chat model messages (chat_message type)
                    if (chat.type === 'chat_message') {
                        return {
                            type: 'chat_message',
                            id: chat.id,
                            chat_id: chat.chat_id,
                            time: chat.time,
                            date: new Date(chat.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
                            customer: chat.fullname,
                            email: chat.email,
                            subject: 'Chat Message',
                            message: chat.message,
                            is_read: chat.is_read
                        };
                    }
                    // Handle ContactMessage chats
                    return {
                        type: 'chat',
                        id: chat.id,
                        time: chat.time,
                        date: new Date(chat.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
                        customer: chat.fullname,
                        email: chat.email,
                        location: chat.location,
                        subject: chat.subject,
                        message: chat.message,
                        is_read: chat.is_read
                    };
                });

                // Keep notifications in the dashboard even after page refresh.
                const allItems = mergeStoredNotifications([...orderItems, ...reviewItems, ...chatItems]);
                saveStoredNotifications(allItems);

                if (allItems.length === 0) {
                    container.innerHTML = `
                        <div style="text-align: center; color: #a1887f; padding: 2rem;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="none" viewBox="0 0 24 24" stroke="#a1887f" stroke-width="1.5" style="display:inline-block;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <p style="margin: 0; color: #6d4c41; font-size: 1.1rem; font-weight: 600;">No Notifications Yet</p>
                            <p style="margin: 0.5rem 0 0 0; color: #a1887f; font-size: 0.9rem;">Orders, reviews, chats and cancellations will appear here</p>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = allItems.map(item => {
                    if (item.type === 'cancellation') {
                        return `
                            <div class="order-notif-item" style="border-left: 3px solid #ff424f;" onclick='showCancellationPopup(${JSON.stringify(item)})'>
                                <div class="order-notif-header">
                                    <h3 class="order-notif-title" style="color: #ff424f;">Cancelled Order from ${item.customer}</h3>
                                    <span class="order-notif-time">${item.time} • ${item.date}</span>
                                </div>
                                <div class="order-notif-details">
                                    <div class="order-detail-row">
                                        <span class="order-detail-label">Product:</span>
                                        <span class="order-detail-value">${formatProduct(item.product)}</span>
                                    </div>
                                    <div class="order-detail-row">
                                        <span class="order-detail-label">Quantity:</span>
                                        <span class="order-detail-value">${item.quantity}</span>
                                    </div>
                                </div>
                                <span class="order-badge" style="background: #ff424f;">Order #${item.id} - Cancelled</span>
                            </div>
                        `;
                    } else if (item.type === 'review') {
                        const stars = '★'.repeat(item.rating) + '☆'.repeat(5 - item.rating);
                        return `
                            <div class="order-notif-item" style="border-left: 3px solid #ffc107;" onclick='showReviewPopupInline(${JSON.stringify(item)})'>
                                <div class="order-notif-header">
                                    <h3 class="order-notif-title" style="color: #f57c00;">New Review from ${item.customer}</h3>
                                    <span class="order-notif-time">${item.time} • ${item.date}</span>
                                </div>
                                <div class="order-notif-details">
                                    <div class="order-detail-row">
                                        <span class="order-detail-label">Product:</span>
                                        <span class="order-detail-value">${item.product}</span>
                                    </div>
                                    <div class="order-detail-row">
                                        <span class="order-detail-label">Rating:</span>
                                        <span class="order-detail-value" style="color: #ffc107;">${stars}</span>
                                    </div>
                                </div>
                                <span class="order-badge" style="background: #fff3e0; color: #e65100;">Review #${item.id}</span>
                            </div>
                        `;
                    } else if (item.type === 'chat') {
                        return `
                            <div class="order-notif-item" style="border-left: 3px solid #2196f3;" onclick='showChatPopupInline(${JSON.stringify(item)})'>
                                <div class="order-notif-header">
                                    <h3 class="order-notif-title" style="color: #2196f3;">Chat from ${item.customer}</h3>
                                    <span class="order-notif-time">${item.time} • ${item.date}</span>
                                </div>
                                <div class="order-notif-details">
                                    <div class="order-detail-row">
                                        <span class="order-detail-label">Subject:</span>
                                        <span class="order-detail-value">${item.subject}</span>
                                    </div>
                                    <div class="order-detail-row">
                                        <span class="order-detail-label">Email:</span>
                                        <span class="order-detail-value">${item.email}</span>
                                    </div>
                                </div>
                                <span class="order-badge" style="background: #e3f2fd; color: #1976d2;">Chat #${item.id}</span>
                            </div>
                        `;
                    } else if (item.type === 'chat_message') {
                        return `
                            <div class="order-notif-item" style="border-left: 3px solid #9c27b0;" onclick='showChatMessagePopupInline(${JSON.stringify(item)})'>
                                <div class="order-notif-header">
                                    <h3 class="order-notif-title" style="color: #9c27b0;">Chat Message from ${item.customer}</h3>
                                    <span class="order-notif-time">${item.time} • ${item.date}</span>
                                </div>
                                <div class="order-notif-details">
                                    <div class="order-detail-row">
                                        <span class="order-detail-label">Message:</span>
                                        <span class="order-detail-value">${item.message.substring(0, 50)}${item.message.length > 50 ? '...' : ''}</span>
                                    </div>
                                    <div class="order-detail-row">
                                        <span class="order-detail-label">Email:</span>
                                        <span class="order-detail-value">${item.email}</span>
                                    </div>
                                </div>
                                <span class="order-badge" style="background: #f3e5f5; color: #7b1fa2;">Msg #${item.id}</span>
                            </div>
                        `;
                    } else {
                        return `
                            <div class="order-notif-item" onclick='showOrderPopupInline(${JSON.stringify(item)})'>
                                <div class="order-notif-header">
                                    <h3 class="order-notif-title">New Order from ${item.customer}</h3>
                                    <span class="order-notif-time">${item.time} • ${item.date}</span>
                                </div>
                                <div class="order-notif-details">
                                    <div class="order-detail-row">
                                        <span class="order-detail-label">Product:</span>
                                        <span class="order-detail-value">${formatProduct(item.product)}</span>
                                    </div>
                                    <div class="order-detail-row">
                                        <span class="order-detail-label">Quantity:</span>
                                        <span class="order-detail-value">${item.quantity}</span>
                                    </div>
                                </div>
                                <span class="order-badge">Order #${item.id}</span>
                            </div>
                        `;
                    }
                }).join('');
            }

            function formatProduct(product) {
                const names = {
                    'quail_eggs': 'Quail Eggs',
                    'live_quail': 'Live Quail',
                    'dressed_quail': 'Dressed Quail',
                    'mixed': 'Mixed Order',
                    'fresh-quail-eggs': 'Fresh Quail Eggs',
                    'live-quail': 'Live Quail',
                    'dressed-quail': 'Dressed Quail'
                };
                return names[product] || product || 'N/A';
            }

            function extractDetail(message, key) {
                if (!message) return 'N/A';
                const lines = message.split('\n');
                for (const line of lines) {
                    const parts = line.split(':', 2);
                    if (parts.length === 2 && parts[0].trim() === key) {
                        return parts[1].trim();
                    }
                }
                return 'N/A';
            }

            let currentPopupOrderId = null;

            function showOrderPopup(order) {
                showOrderPopupInline(order);
            }

            function closeOrderPopup() {
                document.getElementById('orderPopupModal').style.display = 'none';
                if (currentPopupOrderId) {
                    acknowledgeItem('order', currentPopupOrderId);
                    currentPopupOrderId = null;
                }
            }

            function showOrderPopupInline(order) {
                currentPopupOrderId = order.id;
                document.getElementById('popupCustomerName').textContent = order.customer;
                document.getElementById('popupProduct').textContent = formatProduct(order.product);
                document.getElementById('popupQuantity').textContent = order.quantity;
                document.getElementById('popupContact').textContent = order.contact;
                document.getElementById('popupTime').textContent = order.time;
                document.getElementById('popupOrderId').textContent = '#' + order.id;
                document.getElementById('orderPopupModal').style.display = 'flex';
            }

            function showReviewPopupInline(review) {
                currentPopupReviewId = review.id;
                const stars = '★'.repeat(review.rating) + '☆'.repeat(5 - review.rating);
                document.getElementById('reviewPopupCustomer').textContent = review.customer;
                document.getElementById('reviewPopupProduct').textContent = review.product;
                document.getElementById('reviewPopupRating').textContent = stars;
                document.getElementById('reviewPopupComment').textContent = review.comment || 'No comment';
                document.getElementById('reviewPopupTime').textContent = review.time;
                document.getElementById('reviewPopupModal').style.display = 'flex';
            }

            let currentPopupCancellationId = null;

            function showCancellationPopup(order) {
                currentPopupCancellationId = order.id;
                document.getElementById('cancellationPopupCustomer').textContent = order.customer;
                document.getElementById('cancellationPopupProduct').textContent = formatProduct(order.product);
                document.getElementById('cancellationPopupQuantity').textContent = order.quantity;
                document.getElementById('cancellationPopupReason').textContent = order.reason || 'Not specified';
                document.getElementById('cancellationPopupTime').textContent = order.time;
                document.getElementById('cancellationPopupModal').style.display = 'flex';
            }

            function closeCancellationPopup() {
                document.getElementById('cancellationPopupModal').style.display = 'none';
                if (currentPopupCancellationId) {
                    acknowledgeItem('cancellation', currentPopupCancellationId);
                    currentPopupCancellationId = null;
                }
            }

            function showChatPopup(chat) {
                currentChatType = 'contact_chat';
                currentChatId = chat.id;
                document.getElementById('chatPopupCustomer').textContent = chat.fullname;
                document.getElementById('chatPopupSubject').textContent = chat.subject;
                document.getElementById('chatPopupEmail').textContent = chat.email;
                document.getElementById('chatPopupLocation').textContent = chat.location || 'N/A';
                document.getElementById('chatPopupMessage').textContent = chat.message;
                document.getElementById('chatPopupTime').textContent = chat.time;
                document.getElementById('chatPopupModal').style.display = 'flex';
            }

            function showChatPopupInline(chat) {
                currentChatType = 'contact_chat';
                currentChatId = chat.id;
                document.getElementById('chatPopupCustomer').textContent = chat.customer;
                document.getElementById('chatPopupSubject').textContent = chat.subject;
                document.getElementById('chatPopupEmail').textContent = chat.email;
                document.getElementById('chatPopupLocation').textContent = chat.location || 'N/A';
                document.getElementById('chatPopupMessage').textContent = chat.message;
                document.getElementById('chatPopupTime').textContent = chat.time;
                document.getElementById('chatPopupModal').style.display = 'flex';
            }

            function showChatMessagePopup(chat) {
                currentChatType = 'chat_message';
                currentChatId = chat.id;
                document.getElementById('chatPopupCustomer').textContent = chat.fullname;
                document.getElementById('chatPopupSubject').textContent = 'Chat Message';
                document.getElementById('chatPopupEmail').textContent = chat.email;
                document.getElementById('chatPopupLocation').textContent = 'N/A';
                document.getElementById('chatPopupMessage').textContent = chat.message;
                document.getElementById('chatPopupTime').textContent = chat.time;
                document.getElementById('chatPopupModal').style.display = 'flex';
            }

            function showChatMessagePopupInline(chat) {
                currentChatType = 'chat_message';
                currentChatId = chat.id;
                document.getElementById('chatPopupCustomer').textContent = chat.customer;
                document.getElementById('chatPopupSubject').textContent = 'Chat Message';
                document.getElementById('chatPopupEmail').textContent = chat.email;
                document.getElementById('chatPopupLocation').textContent = 'N/A';
                document.getElementById('chatPopupMessage').textContent = chat.message;
                document.getElementById('chatPopupTime').textContent = chat.time;
                document.getElementById('chatPopupModal').style.display = 'flex';
            }

            let currentChatType = null;
            let currentChatId = null;

            function closeChatPopup() {
                document.getElementById('chatPopupModal').style.display = 'none';
                if (currentChatType && currentChatId) {
                    acknowledgeItem(currentChatType, currentChatId);
                    currentChatType = null;
                    currentChatId = null;
                }
            }


            function playOrderSound() {
                // Two-tone "ding-dong" chime for order/review/chat/cancellation popups
                try {
                    if (!window.__sqfmAudioCtx) {
                        const AC = window.AudioContext || window.webkitAudioContext;
                        if (AC) window.__sqfmAudioCtx = new AC();
                    }
                    const ctx = window.__sqfmAudioCtx;
                    if (!ctx) return;
                    if (ctx.state === 'suspended') ctx.resume();
                    [[880, 0], [1175, 0.22]].forEach(function (pair) {
                        const freq = pair[0], delay = pair[1];
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'sine';
                        osc.frequency.value = freq;
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        const t0 = ctx.currentTime + delay;
                        gain.gain.setValueAtTime(0.0001, t0);
                        gain.gain.exponentialRampToValueAtTime(0.3, t0 + 0.02);
                        gain.gain.exponentialRampToValueAtTime(0.0001, t0 + 0.45);
                        osc.start(t0);
                        osc.stop(t0 + 0.5);
                    });
                } catch (e) { /* sound must never break notifications */ }
            }

            // Unlock audio on the first user gesture (browser autoplay policy)
            ['click', 'keydown', 'touchstart'].forEach(function (evt) {
                document.addEventListener(evt, function sqfmAudioUnlock() {
                    if (window.__sqfmAudioCtx && window.__sqfmAudioCtx.state === 'suspended') {
                        window.__sqfmAudioCtx.resume();
                    }
                    document.removeEventListener(evt, sqfmAudioUnlock);
                }, { once: true });
            });
            
            function clearAllOrderNotifications() {
                if (confirm('This will hide all notifications. Continue?')) {
                    const container = document.getElementById('orderNotificationsContainer');
                    if (container) {
                        localStorage.removeItem('dashboard_notifications');
                        // Also clear DB acknowledgments so state matches across devices
                        fetch('/api/admin/notification-acks/clear', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' }
                        }).catch(() => {});
                        container.innerHTML = `
                            <div style="text-align: center; color: #a1887f; padding: 2rem;">
                                <div style="font-size: 3rem; margin-bottom: 1rem;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="none" viewBox="0 0 24 24" stroke="#a1887f" stroke-width="1.5" style="display:inline-block;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </div>
                                <p style="margin: 0; color: #6d4c41; font-size: 1.1rem; font-weight: 600;">No Notifications Yet</p>
                                <p style="margin: 0.5rem 0 0 0; color: #a1887f; font-size: 0.9rem;">New notifications will appear here</p>
                            </div>
                        `;
                    }
                }
            }
            
            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                loadServerAcks();
                loadOrderNotifications();
                
                // Check for new orders every 10 seconds
                setInterval(loadOrderNotifications, 10000);
            });
        </script>
    </div>

    <!-- Order Popup Modal -->
    <div id="orderPopupModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 10000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 24px; padding: 2.5rem; max-width: 500px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); animation: slideDown 0.3s ease;">
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #6d4c41, #8d6e63); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; box-shadow: 0 8px 24px rgba(109,76,65,0.3);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <h2 style="font-size: 1.5rem; font-weight: 800; color: #6d4c41; margin: 0 0 0.5rem 0;">New Order Received!</h2>
                <p style="color: #8d6e63; font-size: 0.9rem; margin: 0;" id="popupTime">Just now</p>
            </div>
            
            <div style="background: #f5f0eb; border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem;">
                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; color: #8d6e63; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Customer</div>
                    <div style="font-size: 1.1rem; color: #4e342e; font-weight: 700;" id="popupCustomerName">-</div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <div style="font-size: 0.75rem; color: #8d6e63; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Product</div>
                        <div style="font-size: 0.95rem; color: #4e342e; font-weight: 700;" id="popupProduct">-</div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: #8d6e63; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Quantity</div>
                        <div style="font-size: 0.95rem; color: #4e342e; font-weight: 700;" id="popupQuantity">-</div>
                    </div>
                </div>
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #d7ccc8;">
                    <div style="font-size: 0.75rem; color: #8d6e63; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Contact</div>
                    <div style="font-size: 0.95rem; color: #4e342e; font-weight: 700;" id="popupContact">-</div>
                </div>
            </div>
            
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <span style="display: inline-block; padding: 0.5rem 1rem; background: #e8f5e9; color: #2e7d32; border-radius: 20px; font-size: 0.85rem; font-weight: 700;" id="popupOrderId">#0</span>
            </div>
            
            <button onclick="closeOrderPopup()" style="width: 100%; padding: 1rem; background: linear-gradient(135deg, #6d4c41, #4e342e); color: white; border: none; border-radius: 12px; font-size: 1rem; font-weight: 700; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(78,52,46,0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                Got it!
            </button>
        </div>
    </div>

    <!-- Review Popup Modal -->
    <div id="reviewPopupModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 10000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 24px; padding: 2.5rem; max-width: 500px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); animation: slideDown 0.3s ease;">
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #ffc107, #ff9800); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; box-shadow: 0 8px 24px rgba(255,193,7,0.3);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
                <h2 style="font-size: 1.5rem; font-weight: 800; color: #6d4c41; margin: 0 0 0.5rem 0;">New Customer Review!</h2>
                <p style="color: #8d6e63; font-size: 0.9rem; margin: 0;" id="reviewPopupTime">Just now</p>
            </div>

            <div style="background: #f5f0eb; border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem;">
                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; color: #8d6e63; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Customer</div>
                    <div style="font-size: 1.1rem; color: #4e342e; font-weight: 700;" id="reviewPopupCustomer">-</div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <div style="font-size: 0.75rem; color: #8d6e63; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Product</div>
                        <div style="font-size: 0.95rem; color: #4e342e; font-weight: 700;" id="reviewPopupProduct">-</div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: #8d6e63; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Rating</div>
                        <div style="font-size: 1.2rem; color: #ffc107; font-weight: 700;" id="reviewPopupRating">-</div>
                    </div>
                </div>
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #d7ccc8;">
                    <div style="font-size: 0.75rem; color: #8d6e63; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Comment</div>
                    <div style="font-size: 0.95rem; color: #4e342e; font-style: italic;" id="reviewPopupComment">-</div>
                </div>
            </div>

            <button onclick="closeReviewPopup()" style="width: 100%; padding: 1rem; background: linear-gradient(135deg, #ffc107, #ff9800); color: white; border: none; border-radius: 12px; font-size: 1rem; font-weight: 700; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(255,193,7,0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                Got it!
            </button>
        </div>
    </div>

    <!-- Cancellation Popup Modal -->
    <div id="cancellationPopupModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 10000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 24px; padding: 2.5rem; max-width: 500px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); animation: slideDown 0.3s ease;">
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #ff424f, #ff8a80); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; box-shadow: 0 8px 24px rgba(255,66,79,0.3);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h2 style="font-size: 1.5rem; font-weight: 800; color: #ff424f; margin: 0 0 0.5rem 0;">Order Cancelled!</h2>
                <p style="color: #8d6e63; font-size: 0.9rem; margin: 0;" id="cancellationPopupTime">Just now</p>
            </div>

            <div style="background: #f5f0eb; border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem;">
                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; color: #8d6e63; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Customer</div>
                    <div style="font-size: 1.1rem; color: #4e342e; font-weight: 700;" id="cancellationPopupCustomer">-</div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <div style="font-size: 0.75rem; color: #8d6e63; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Product</div>
                        <div style="font-size: 0.95rem; color: #4e342e; font-weight: 700;" id="cancellationPopupProduct">-</div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: #8d6e63; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Quantity</div>
                        <div style="font-size: 0.95rem; color: #4e342e; font-weight: 700;" id="cancellationPopupQuantity">-</div>
                    </div>
                </div>
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #d7ccc8;">
                    <div style="font-size: 0.75rem; color: #8d6e63; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Reason for Cancellation</div>
                    <div style="font-size: 0.95rem; color: #ff424f; font-weight: 600;" id="cancellationPopupReason">-</div>
                </div>
            </div>

            <button onclick="closeCancellationPopup()" style="width: 100%; padding: 1rem; background: linear-gradient(135deg, #ff424f, #ff8a80); color: white; border: none; border-radius: 12px; font-size: 1rem; font-weight: 700; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(255,66,79,0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                Got it!
            </button>
        </div>
    </div>

    <!-- Chat Popup Modal -->
    <div id="chatPopupModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 10000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 24px; padding: 2.5rem; max-width: 500px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); animation: slideDown 0.3s ease;">
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #2196f3, #64b5f6); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; box-shadow: 0 8px 24px rgba(33,150,243,0.3);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <h2 style="font-size: 1.5rem; font-weight: 800; color: #2196f3; margin: 0 0 0.5rem 0;">New Chat Message!</h2>
                <p style="color: #8d6e63; font-size: 0.9rem; margin: 0;" id="chatPopupTime">Just now</p>
            </div>

            <div style="background: #f5f0eb; border-radius: 16px; padding: 1.5rem; margin-bottom: 1.5rem;">
                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; color: #8d6e63; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Customer</div>
                    <div style="font-size: 1.1rem; color: #4e342e; font-weight: 700;" id="chatPopupCustomer">-</div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <div style="font-size: 0.75rem; color: #8d6e63; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Subject</div>
                        <div style="font-size: 0.95rem; color: #4e342e; font-weight: 700;" id="chatPopupSubject">-</div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: #8d6e63; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Email</div>
                        <div style="font-size: 0.95rem; color: #4e342e; font-weight: 700;" id="chatPopupEmail">-</div>
                    </div>
                </div>
                <div style="margin-top: 1rem;">
                    <div style="font-size: 0.75rem; color: #8d6e63; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Location</div>
                    <div style="font-size: 0.95rem; color: #4e342e; font-weight: 700;" id="chatPopupLocation">-</div>
                </div>
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #d7ccc8;">
                    <div style="font-size: 0.75rem; color: #8d6e63; font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem;">Message</div>
                    <div style="font-size: 0.95rem; color: #4e342e; line-height: 1.5;" id="chatPopupMessage">-</div>
                </div>
            </div>

            <button onclick="closeChatPopup()" style="width: 100%; padding: 1rem; background: linear-gradient(135deg, #2196f3, #64b5f6); color: white; border: none; border-radius: 12px; font-size: 1rem; font-weight: 700; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(33,150,243,0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                Got it!
            </button>
        </div>
    </div>

    <style>
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-30px) scale(0.9); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
    </style>
    
    <!-- Breed Selector Modal - Removed -->
</div>

<!-- Feeding Notification Modal -->
<div id="feedingNotificationModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:white;border-radius:24px;padding:2.5rem;text-align:center;box-shadow:0 24px 64px rgba(76,175,80,0.4);width:420px;animation:slideIn 0.3s ease;border:3px solid #4caf50;">
        <div style="font-size:3rem;margin-bottom:0.5rem;">🍽️</div>
        <div style="font-size:1.5rem;font-weight:800;color:#2e7d32;margin-bottom:0.75rem;">Feeding Time!</div>
        <div style="font-size:1.1rem;color:#6d4c41;margin-bottom:0.5rem;font-weight:600;">
            The quails are now being fed.
        </div>
        <div style="background:#f1f8e9;padding:1.5rem;border-radius:12px;margin:1.5rem 0;border:2px solid #c5e1a5;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;text-align:left;margin-bottom:1rem;">
                <div>
                    <div style="font-size:0.85rem;color:#558b2f;font-weight:600;text-transform:uppercase;">Cage</div>
                    <div style="font-size:1.3rem;color:#2e7d32;font-weight:700;" id="feedingModalCage">1</div>
                </div>
                <div>
                    <div style="font-size:0.85rem;color:#558b2f;font-weight:600;text-transform:uppercase;">Duration</div>
                    <div style="font-size:1.3rem;color:#2e7d32;font-weight:700;"><span id="feedingModalDuration">20</span>s</div>
                </div>
            </div>
            <!-- Progress Bar with Percentage -->
            <div style="margin-bottom:1rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
                    <div style="font-size:0.85rem;color:#558b2f;font-weight:600;text-transform:uppercase;">Progress</div>
                    <div style="font-size:1.1rem;color:#2e7d32;font-weight:800;" id="feedingModalPercentage">0%</div>
                </div>
                <div style="width:100%;height:12px;background:#e0e0e0;border-radius:6px;overflow:hidden;">
                    <div id="feedingModalProgressBar" style="height:100%;width:0%;background:linear-gradient(90deg,#4caf50,#66bb6a);border-radius:6px;transition:width 0.1s linear;"></div>
                </div>
                <div style="display:flex;justify-content:space-between;margin-top:0.25rem;">
                    <div style="font-size:0.75rem;color:#689f38;" id="feedingModalElapsed">0s elapsed</div>
                    <div style="font-size:0.75rem;color:#689f38;" id="feedingModalRemaining">20s left</div>
                </div>
            </div>
            <div style="border-top:2px solid #c5e1a5;padding-top:1rem;text-align:center;">
                <div style="font-size:0.85rem;color:#558b2f;font-weight:600;margin-bottom:0.25rem;text-transform:uppercase;">Scheduled Time</div>
                <div style="font-size:1.15rem;color:#2e7d32;font-weight:700;" id="feedingModalTime">--:--</div>
                <div style="font-size:0.9rem;color:#689f38;margin-top:0.25rem;" id="feedingModalDate">--- --, ----</div>
            </div>
        </div>
        <button onclick="confirmFeeding()" 
            style="background:linear-gradient(135deg,#4caf50,#66bb6a);color:white;border:none;padding:0.85rem 2rem;border-radius:10px;font-weight:700;font-size:1.05rem;cursor:pointer;width:100%;box-shadow:0 4px 12px rgba(76,175,80,0.3);">
            OK, Got it
        </button>
    </div>
</div>

<style>
@keyframes slideIn {
    from { opacity: 0; transform: translateY(-20px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
</style>

<script>
// Breed selector functions removed
</script>

<script>
// ── Feeding Notification Modal with Progress Bar ──
let feedingStartTime = null;
let feedingDuration = 0;
let feedingProgressInterval = null;
let feedingNotificationShown = false;

function startFeedingProgress(duration) {
    feedingStartTime = Date.now();
    feedingDuration = duration;
    
    // Clear any existing interval
    if (feedingProgressInterval) {
        clearInterval(feedingProgressInterval);
    }
    
    // Update progress every 100ms for smooth animation
    feedingProgressInterval = setInterval(function() {
        const elapsed = (Date.now() - feedingStartTime) / 1000;
        const percentage = Math.min((elapsed / feedingDuration) * 100, 100);
        const remaining = Math.max(feedingDuration - elapsed, 0);
        
        // Update progress bar
        const progressBar = document.getElementById('feedingModalProgressBar');
        const percentageEl = document.getElementById('feedingModalPercentage');
        const elapsedEl = document.getElementById('feedingModalElapsed');
        const remainingEl = document.getElementById('feedingModalRemaining');
        
        if (progressBar) {
            progressBar.style.width = percentage + '%';
        }
        if (percentageEl) {
            percentageEl.textContent = Math.round(percentage) + '%';
        }
        if (elapsedEl) {
            elapsedEl.textContent = Math.round(elapsed) + 's elapsed';
        }
        if (remainingEl) {
            remainingEl.textContent = Math.round(remaining) + 's left';
        }
        
        // Change color based on progress
        if (progressBar) {
            if (percentage < 30) {
                progressBar.style.background = 'linear-gradient(90deg,#ff9800,#ffc107)';
            } else if (percentage < 70) {
                progressBar.style.background = 'linear-gradient(90deg,#2196f3,#42a5f5)';
            } else if (percentage < 100) {
                progressBar.style.background = 'linear-gradient(90deg,#4caf50,#66bb6a)';
            } else {
                progressBar.style.background = 'linear-gradient(90deg,#2e7d32,#388e3c)';
            }
        }
        
        // Stop at 100%
        if (percentage >= 100) {
            clearInterval(feedingProgressInterval);
            feedingProgressInterval = null;
        }
    }, 100);
}

function stopFeedingProgress() {
    if (feedingProgressInterval) {
        clearInterval(feedingProgressInterval);
        feedingProgressInterval = null;
    }
}

function showFeedingModal(data) {
    if (feedingNotificationShown) return;
    feedingNotificationShown = true;
    
    // Set modal content
    const cage = data.cage || data.cage_number || 1;
    const duration = data.duration || 10;
    const startedAt = data.started_at || new Date().toISOString();
    
    document.getElementById('feedingModalCage').textContent = cage;
    document.getElementById('feedingModalDuration').textContent = duration;
    
    // Parse and display time
    const startDate = new Date(startedAt);
    const timeStr = startDate.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
    const dateStr = startDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    document.getElementById('feedingModalTime').textContent = timeStr;
    document.getElementById('feedingModalDate').textContent = dateStr;
    
    // Reset and start progress bar
    document.getElementById('feedingModalProgressBar').style.width = '0%';
    document.getElementById('feedingModalPercentage').textContent = '0%';
    document.getElementById('feedingModalElapsed').textContent = '0s elapsed';
    document.getElementById('feedingModalRemaining').textContent = duration + 's left';
    
    // Show modal
    document.getElementById('feedingNotificationModal').style.display = 'flex';
    
    // Start progress bar
    startFeedingProgress(duration);
}

function hideFeedingModal() {
    document.getElementById('feedingNotificationModal').style.display = 'none';
    stopFeedingProgress();
    feedingNotificationShown = false;
}

function confirmFeeding() {
    // Stop progress bar
    stopFeedingProgress();
    
    // Hide modal
    hideFeedingModal();
    
    // Call API to confirm feeding (deletes notification file)
    fetch('/api/feeder/confirm-feeding', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(r => r.json())
    .then(data => {
        console.log('Feeding confirmed:', data);
    })
    .catch(err => {
        console.log('Error confirming feeding:', err);
    });
}

// Poll for feeding notifications
function checkFeedingNotification() {
    if (feedingNotificationShown) return;
    
    fetch('/api/feeder/feeding-notification', { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            if (data && data.active) {
                showFeedingModal(data);
            }
        })
        .catch(() => {});
}

// Start polling for feeding notifications every 3 seconds
document.addEventListener('DOMContentLoaded', function() {
    setInterval(checkFeedingNotification, 3000);
    checkFeedingNotification(); // Check immediately on page load
});
</script>

<script>
function updateFoodLevel() {
    fetch('/api/food-level/current', { cache: 'no-store' })
        .then(r => r.json())
        .then(data => {
            const level = data.level ?? 0;
            const status = data.status ?? 'normal';
            const detected = data.detected !== false;
            const el = document.getElementById('foodLevelValue');
            const bar = document.getElementById('foodLevelBar');
            const statusEl = document.getElementById('foodLevelStatus');
            if (!el) return;
            // Show percentage or status text
            if (status === 'empty' || !detected) {
                el.textContent = 'No food';
                bar.style.width = '0%';
                bar.style.background = '#f44336';
                el.style.color = '#f44336';
                statusEl.textContent = 'Food container is empty';
            } else if (status === 'low') {
                el.textContent = level + '%';
                bar.style.width = level + '%';
                bar.style.background = '#ff9800';
                el.style.color = '#ff9800';
                statusEl.textContent = 'Low food level';
            } else {
                el.textContent = level + '%';
                bar.style.width = level + '%';
                bar.style.background = '#4caf50';
                el.style.color = '#6d4c41';
                statusEl.textContent = 'Food detected';
            }
            // Trigger notification (once per state change)
            if (typeof notifyFoodLevel === 'function') {
                notifyFoodLevel(level, level);
            }
        })
        .catch(() => {
            const el = document.getElementById('foodLevelValue');
            if (el) el.textContent = '--%';
        });
}
document.addEventListener('DOMContentLoaded', function() {
    updateFoodLevel();
    setInterval(updateFoodLevel, 2000);
});
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ── Egg Production Trend Chart (Last 7 Days) ──
let eggTrendChart = null;
function renderEggTrendChart(trend) {
    const ctx = document.getElementById('eggTrendChart');
    if (!ctx || typeof Chart === 'undefined') return;
    if (eggTrendChart) eggTrendChart.destroy();
    eggTrendChart = new Chart(ctx.getContext('2d'), {
        type: 'line',
        data: {
            labels: trend.labels,
            datasets: [
                {
                    label: 'Total Eggs',
                    data: trend.totals,
                    borderColor: '#795548',
                    backgroundColor: 'rgba(121, 85, 72, 0.15)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#795548'
                },
                {
                    label: 'Good Eggs',
                    data: trend.good,
                    borderColor: '#4caf50',
                    backgroundColor: 'rgba(76, 175, 80, 0.10)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#4caf50'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { labels: { color: '#6d4c41' } } },
            scales: {
                x: { ticks: { color: '#8d6e63' }, grid: { display: false } },
                y: { beginAtZero: true, ticks: { color: '#8d6e63', precision: 0 }, grid: { color: 'rgba(215, 204, 200, 0.4)' } }
            }
        }
    });
}

// ── Live Dashboard Summary Polling (eggs, sales, orders, sensor, inventory, feed) ──
function formatPeso(n) {
    return '₱' + Number(n || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function updateInventoryCard(inv) {
    const valueEl = document.getElementById('inventoryStatusValue');
    const detailEl = document.getElementById('inventoryStatusDetail');
    if (!valueEl) return;
    const colors = { out: '#d32f2f', low: '#ff9800', ok: '#6d4c41' };
    valueEl.textContent = inv.label;
    valueEl.style.color = colors[inv.level] || '#6d4c41';
    if (detailEl) detailEl.textContent = inv.detail;
}

let lastEnvironmentNotificationState = null;

function updateDashboardSummary(data) {
    // Today's egg summary
    if (data.eggs) {
        const totalEl = document.getElementById('totalEggsToday');
        const goodEl = document.getElementById('goodEggsToday');
        const crackedEl = document.getElementById('crackedEggsToday');
        if (totalEl) totalEl.textContent = data.eggs.total + ' eggs';
        if (goodEl) goodEl.textContent = data.eggs.good + ' eggs';
        if (crackedEl) crackedEl.textContent = data.eggs.cracked + ' eggs';
    }

    // Sales & Orders cards
    if (data.sales) {
        const stv = document.getElementById('salesTodayValue');
        const stc = document.getElementById('salesTodayCount');
        const trv = document.getElementById('totalRevenueValue');
        const tsc = document.getElementById('totalSalesCount');
        if (stv) stv.textContent = formatPeso(data.sales.today_revenue);
        if (stc) stc.textContent = data.sales.today_count + ' sale(s)';
        if (trv) trv.textContent = formatPeso(data.sales.total_revenue);
        if (tsc) tsc.textContent = data.sales.total_count + ' total sale(s)';
    }
    if (data.orders) {
        const otv = document.getElementById('ordersTodayValue');
        const pov = document.getElementById('pendingOrdersValue');
        if (otv) otv.textContent = data.orders.today;
        if (pov) pov.textContent = data.orders.pending;
        document.querySelectorAll('#orderStatusChips [data-status-count]').forEach(chip => {
            const status = chip.getAttribute('data-status-count');
            const label = chip.textContent.split(':')[0].trim();
            chip.textContent = label + ': ' + (data.orders[status] ?? 0);
        });
    }

    // Temperature & Humidity card
    if (data.sensor) {
        const tempEl = document.getElementById('tempValue');
        const humEl = document.getElementById('humidityValue');
        const updEl = document.getElementById('sensorUpdatedAt');
        const temperature = data.sensor.temperature;
        const humidity = data.sensor.humidity;
        if (tempEl) tempEl.textContent = temperature !== null ? temperature.toFixed(1) + '°C' : '--°C';
        if (humEl) humEl.textContent = humidity !== null ? humidity.toFixed(1) + '%' : '--%';
        if (updEl) updEl.textContent = data.sensor.recorded_at ? 'Updated ' + data.sensor.recorded_at : 'No recent sensor data';

        if (temperature !== null && humidity !== null && typeof notifyTemperatureHumidityStatus === 'function') {
            const status = temperature > 35 || humidity > 85
                ? 'critical'
                : temperature < 25 || humidity < 60
                    ? 'warning'
                    : 'normal';
            if (status !== lastEnvironmentNotificationState) {
                lastEnvironmentNotificationState = status;
                const message = `Temp: ${temperature.toFixed(1)}°C, Humidity: ${humidity.toFixed(1)}%`;
                notifyTemperatureHumidityStatus(status, temperature, humidity, message);
            }
        }
    }

    // Inventory card
    if (data.inventory) updateInventoryCard(data.inventory);

    // Feeding Activity card
    if (data.feed) {
        const ftEl = document.getElementById('feedsTodayValue');
        const lfEl = document.getElementById('lastFedValue');
        if (ftEl) ftEl.textContent = data.feed.feeds_today + ' feed(s) today';
        if (lfEl) lfEl.textContent = 'Last fed: ' + (data.feed.last_fed_at || 'No record yet');
    }
}

function loadDashboardSummary() {
    fetch('/api/dashboard/summary', { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(updateDashboardSummary)
        .catch(() => {});
}

// ── Philippine Weather Widget ──
let weatherFailed = false;
function updateWeather() {
    fetch('/api/weather/philippines')
        .then(r => r.json())
        .then(data => {
            const tempEl = document.getElementById('weatherTemp');
            const descEl = document.getElementById('weatherDesc');
            if (!tempEl) return;
            if (data && data.success) {
                let desc = (data.description || '').charAt(0).toUpperCase() + (data.description || '').slice(1);
                if (parseFloat(data.feels_like) >= 33) desc += ' • ⚠️ Heat stress risk!';
                tempEl.textContent = data.temperature + '°C';
                descEl.textContent = desc + ' • Feels like ' + data.feels_like + '°C';
                weatherFailed = false;
            } else {
                throw new Error('bad weather payload');
            }
        })
        .catch(() => {
            if (weatherFailed) return;
            weatherFailed = true;
            const tempEl = document.getElementById('weatherTemp');
            const descEl = document.getElementById('weatherDesc');
            if (tempEl) tempEl.textContent = '--°C';
            if (descEl) descEl.textContent = 'Weather unavailable';
        });
}

document.addEventListener('DOMContentLoaded', function() {
    renderEggTrendChart(@json($eggTrend));
    loadDashboardSummary();
    updateWeather();
    setInterval(loadDashboardSummary, 5000);  // Update every 5 seconds
    setInterval(updateWeather, 600000); // refresh weather every 10 minutes

        // Topbar date
    const topbarDate = document.getElementById('topbar-date');
    if (topbarDate) {
        topbarDate.textContent = new Date().toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
});
</script>
</div>
</div>

<!-- Order Notification System - Orders Only -->
<script src="{{ asset('order-notifications.js') }}"></script>

@endsection
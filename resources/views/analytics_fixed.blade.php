@extends('layouts.app')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #f5f0eb 0%, #efe8e4 50%, #e8dcd6 100%);
        min-height: 100vh;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    
    .nav-bar {
        background: linear-gradient(135deg, #fffdfa 0%, #f5eee6 100%);
        border-bottom: 2px solid #d7ccc8;
        box-shadow: 0 4px 20px rgba(121, 85, 72, 0.08);
        padding: 1.25rem 3rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 1000;
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
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
    }
    .nav-item:hover {
        background: #f8ede3;
        box-shadow: 0 6px 16px rgba(141, 110, 99, 0.18);
        transform: translateY(-1px);
    }
    .nav-item:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(141, 110, 99, 0.15);
    }
    .nav-item.active {
        background: #a1887f;
        color: #fffdfa;
        box-shadow: 0 6px 16px rgba(141, 110, 99, 0.18), 0 0 20px rgba(161, 136, 127, 0.5);
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
    
    .chart-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }
    .chart-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12);
        border: 1px solid rgba(161, 136, 127, 0.1);
    }
    .chart-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #6d4c41;
        margin-bottom: 1.5rem;
    }
    
    @media (max-width: 768px) {
        .nav-menu {
            display: none;
        }
        .container {
            padding: 1.5rem 1rem;
        }
        .chart-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Navigation Bar -->
<div class="nav-bar">
    <div class="logo-section">
        <div class="logo">
            <img src="https://th.bing.com/th/id/R.2f6964b62e896f5f94f40390e330a1f8?rik=xmu%2bEOduizkGYw&riu=http%3a%2f%2f1.bp.blogspot.com%2f-unYxCWFMHHg%2fVkwfiPzQihI%2fAAAAAAAAq2w%2fhxBh0S_eU3A%2fs1600%2fQuail-Bird-Eggs-(4).jpg&ehk=NmS88wB52fy2LF8VygVGin9Yh5Nn9arRYJNlwN1WaGQ%3d&risl=&pid=ImgRaw&r=0" alt="Logo">
        </div>
        <span class="logo-text">SQUIFM</span>
    </div>
    <nav class="nav-menu">
        <a href="{{ route('feeder') }}" class="nav-item" onclick="navigateToPage('{{ route('feeder') }}'); return false;">🌾 Feeder</a>
        <a href="{{ route('inventory') }}" class="nav-item" style="margin-left: auto;" onclick="navigateToPage('{{ route('inventory') }}'); return false;">📦 Inventory</a>
        <a href="{{ route('analytics') }}" class="nav-item active">📈 Analytics</a>
        <a href="{{ route('settings') }}" class="nav-item" onclick="navigateToPage('{{ route('settings') }}'); return false;">⚙️ Settings</a>
        <button type="button" class="nav-item" onclick="showLogoutModal(); return false;">🚪 Logout</button>
    </nav>
</div>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">📈 Analytics & Insights</h1>
    </div>

    <div class="chart-grid">
        <div class="chart-card">
            <h3 class="chart-title">📊 Egg Production Trend</h3>
            <canvas id="productionChart" width="400" height="200"></canvas>
        </div>
        
        <div class="chart-card">
            <h3 class="chart-title">🥧 Production Distribution</h3>
            <canvas id="pieChart" width="300" height="200"></canvas>
        </div>
    </div>

    <div class="chart-grid">
        <div class="chart-card">
            <h3 class="chart-title">🌾 Feed Consumption</h3>
            <canvas id="feedChart" width="400" height="200"></canvas>
        </div>
        
        <div class="chart-card">
            <h3 class="chart-title">💰 Revenue Overview</h3>
            <canvas id="revenueChart" width="300" height="200"></canvas>
        </div>
    </div>
</div>

<!-- Logout Modal -->
<div id="logoutModal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; padding: 2rem; max-width: 400px; width: 90%; box-shadow: 0 20px 60px rgba(109, 76, 65, 0.3); text-align: center;">
        <div style="font-size: 3rem; margin-bottom: 1rem;">🚪</div>
        <h2 style="color: #4e342e; margin-bottom: 0.75rem; font-size: 1.2rem;">Confirm Logout</h2>
        <p style="color: #6d4c41; margin: 1rem 0 1.5rem 0; font-size: 0.95rem; line-height: 1.5;">Are you sure you want to logout from SQUIFM? Any unsaved data will be lost.</p>
        
        <div style="display: flex; gap: 0.75rem; justify-content: center;">
            <button onclick="closeLogoutModal()" style="background: #e0e0e0; color: #666; border: none; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 700; cursor: pointer; transition: all 0.2s ease; flex: 1;">
                Cancel
            </button>
            <button onclick="confirmLogout()" style="background: linear-gradient(135deg, #d32f2f 0%, #c62828 100%); color: white; border: none; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 700; cursor: pointer; transition: all 0.2s ease; flex: 1;">
                Yes, Logout
            </button>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('logout') }}" id="logoutForm" style="display: none;">
    @csrf
</form>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Modal functions
function showLogoutModal() {
    const modal = document.getElementById('logoutModal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

function closeLogoutModal() {
    const modal = document.getElementById('logoutModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function confirmLogout() {
    const form = document.getElementById('logoutForm');
    if (form) {
        form.submit();
    }
}

// Navigation functions
function navigateToPage(url) {
    window.location.href = url;
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('logoutModal');
    if (modal && e.target === modal) {
        closeLogoutModal();
    }
});

// Initialize charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('Analytics page loaded, initializing charts...');
    
    // Production Chart
    const productionCtx = document.getElementById('productionChart');
    if (productionCtx) {
        new Chart(productionCtx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Eggs Produced',
                    data: [850, 920, 880, 950],
                    borderColor: '#a1887f',
                    backgroundColor: 'rgba(161, 136, 127, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#d7ccc8' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Pie Chart
    const pieCtx = document.getElementById('pieChart');
    if (pieCtx) {
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Sold', 'In Stock', 'Damaged'],
                datasets: [{
                    data: [450, 20, 30],
                    backgroundColor: ['#a1887f', '#d7ccc8', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // Feed Chart
    const feedCtx = document.getElementById('feedChart');
    if (feedCtx) {
        new Chart(feedCtx, {
            type: 'bar',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Feed (kg)',
                    data: [45, 48, 42, 50],
                    backgroundColor: 'rgba(161, 136, 127, 0.8)',
                    borderColor: '#a1887f',
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#d7ccc8' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Revenue (₱)',
                    data: [8500, 9200, 8800, 9500],
                    borderColor: '#a1887f',
                    backgroundColor: 'rgba(161, 136, 127, 0.2)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#d7ccc8' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
    
    console.log('All charts initialized successfully');
});
</script>
@endsection
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
        width: 100%;
        box-sizing: border-box;
        margin: 0;
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

@include('components.sidebar')

<div id="main-content-wrap" class="sidebar-content-wrap">
<div class="container" style="padding-top:2.5rem;">
    <div class="page-header">
        <h1 class="page-title">📈 Analytics & Insights</h1>
    </div>

    <!-- Date Range Filter and Update Button -->
    <div style="background: white; border-radius: 20px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12); border: 1px solid rgba(161, 136, 127, 0.1);">
        <h3 style="color: #6d4c41; margin-bottom: 1rem; font-size: 1.1rem;">📅 Date Range Filter</h3>
        <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <div>
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #6d4c41; margin-bottom: 0.5rem;">From Date:</label>
                <input type="date" id="analyticsFromDate" style="padding: 0.75rem 1rem; border: 1.5px solid #d7ccc8; border-radius: 10px; font-size: 0.95rem; background: #f9fafb;">
            </div>
            <div>
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #6d4c41; margin-bottom: 0.5rem;">To Date:</label>
                <input type="date" id="analyticsToDate" style="padding: 0.75rem 1rem; border: 1.5px solid #d7ccc8; border-radius: 10px; font-size: 0.95rem; background: #f9fafb;">
            </div>
            <div style="margin-top: 1.5rem;">
                <button onclick="updateAnalytics()" style="background: linear-gradient(135deg, #a1887f 0%, #8d6e63 100%); color: white; border: none; border-radius: 10px; padding: 0.75rem 2rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(141, 110, 99, 0.3);">🔄 Update Analytics</button>
            </div>
        </div>
    </div>

    <!-- Analytics Overview Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
        <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12); border: 1px solid rgba(161, 136, 127, 0.1); text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">🥚</div>
            <div style="font-size: 1.5rem; font-weight: 700; color: #2e7d32;" id="analyticsEggsCollected">0 eggs</div>
            <div style="font-size: 0.85rem; color: #4caf50;">Total Eggs Collected</div>
        </div>
        <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12); border: 1px solid rgba(161, 136, 127, 0.1); text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">💰</div>
            <div style="font-size: 1.5rem; font-weight: 700; color: #2e7d32;" id="analyticsTotalRevenue">₱0.00</div>
            <div style="font-size: 0.85rem; color: #4caf50;">Total Revenue</div>
        </div>
        <div style="background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12); border: 1px solid rgba(161, 136, 127, 0.1); text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">📊</div>
            <div style="font-size: 1.5rem; font-weight: 700; color: #1976d2;" id="analyticsActiveDays">0 days</div>
            <div style="font-size: 0.85rem; color: #2196f3;">Active Days</div>
        </div>
    </div>

    <div class="chart-grid">
        <div class="chart-card">
            <h3 class="chart-title">📊 Egg Production Trend</h3>
            <div id="productionChartLoader" style="text-align: center; padding: 2rem; color: #a1887f;">Loading chart...</div>
            <canvas id="productionChart" width="400" height="200" style="display: none;"></canvas>
        </div>
        
        <div class="chart-card">
            <h3 class="chart-title">🥧 Production Distribution</h3>
            <div id="pieChartLoader" style="text-align: center; padding: 2rem; color: #a1887f;">Loading chart...</div>
            <canvas id="pieChart" width="300" height="200" style="display: none;"></canvas>
        </div>
    </div>

    <div class="chart-grid">
        <div class="chart-card">
            <h3 class="chart-title">🌾 Feed Consumption</h3>
            <div id="feedChartLoader" style="text-align: center; padding: 2rem; color: #a1887f;">Loading chart...</div>
            <canvas id="feedChart" width="400" height="200" style="display: none;"></canvas>
        </div>
        
        <div class="chart-card">
            <h3 class="chart-title">💰 Best Selling Products</h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden;">
                    <thead>
                        <tr style="background: #f5f5f5;">
                            <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.9rem;">Product</th>
                            <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.9rem;">Quantity Sold</th>
                            <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.9rem;">Unit Price</th>
                            <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.9rem;">Total Revenue</th>
                            <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.9rem;">Percentage</th>
                        </tr>
                    </thead>
                    <tbody id="bestSellingTableBody">
                        <tr>
                            <td style="padding: 1rem; font-size: 0.9rem; color: #000;">🥚 Quail Eggs</td>
                            <td style="padding: 1rem; font-size: 0.9rem; color: #000;">45 trays</td>
                            <td style="padding: 1rem; font-size: 0.9rem; color: #000;">₱80.00</td>
                            <td style="padding: 1rem; font-size: 0.9rem; color: #000; font-weight: 700;">₱3,600.00</td>
                            <td style="padding: 1rem; font-size: 0.9rem; color: #000;">65%</td>
                        </tr>
                        <tr style="background: #f9f9f9;">
                            <td style="padding: 1rem; font-size: 0.9rem; color: #000;">🐦 Live Quail</td>
                            <td style="padding: 1rem; font-size: 0.9rem; color: #000;">12 pcs</td>
                            <td style="padding: 1rem; font-size: 0.9rem; color: #000;">₱180.00</td>
                            <td style="padding: 1rem; font-size: 0.9rem; color: #000; font-weight: 700;">₱2,160.00</td>
                            <td style="padding: 1rem; font-size: 0.9rem; color: #000;">25%</td>
                        </tr>
                        <tr>
                            <td style="padding: 1rem; font-size: 0.9rem; color: #000;">🍗 Dressed Quail</td>
                            <td style="padding: 1rem; font-size: 0.9rem; color: #000;">8 pcs</td>
                            <td style="padding: 1rem; font-size: 0.9rem; color: #000;">₱250.00</td>
                            <td style="padding: 1rem; font-size: 0.9rem; color: #000; font-weight: 700;">₱2,000.00</td>
                            <td style="padding: 1rem; font-size: 0.9rem; color: #000;">10%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
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
// Simple chart initialization
document.addEventListener('DOMContentLoaded', function() {
    console.log('Analytics page loaded, initializing charts...');
    
    setTimeout(function() {
        initializeAllCharts();
    }, 500);
});

function initializeAllCharts() {
    console.log('Initializing all charts...');
    
    // Set default dates
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    
    const fromDateInput = document.getElementById('analyticsFromDate');
    const toDateInput = document.getElementById('analyticsToDate');
    
    if (fromDateInput) fromDateInput.value = thirtyDaysAgo.toISOString().split('T')[0];
    if (toDateInput) toDateInput.value = today.toISOString().split('T')[0];
    
    // Update analytics cards with sample data
    updateAnalyticsCards();
    
    // Production Chart
    try {
        const productionCtx = document.getElementById('productionChart');
        const productionLoader = document.getElementById('productionChartLoader');
        
        if (productionCtx && Chart) {
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
            
            if (productionLoader) productionLoader.style.display = 'none';
            productionCtx.style.display = 'block';
            console.log('✅ Production chart created');
        }
    } catch (error) {
        console.error('❌ Production chart error:', error);
    }

    // Pie Chart
    try {
        const pieCtx = document.getElementById('pieChart');
        const pieLoader = document.getElementById('pieChartLoader');
        
        if (pieCtx && Chart) {
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
            
            if (pieLoader) pieLoader.style.display = 'none';
            pieCtx.style.display = 'block';
            console.log('✅ Pie chart created');
        }
    } catch (error) {
        console.error('❌ Pie chart error:', error);
    }

    // Feed Chart
    try {
        const feedCtx = document.getElementById('feedChart');
        const feedLoader = document.getElementById('feedChartLoader');
        
        if (feedCtx && Chart) {
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
            
            if (feedLoader) feedLoader.style.display = 'none';
            feedCtx.style.display = 'block';
            console.log('✅ Feed chart created');
        }
    } catch (error) {
        console.error('❌ Feed chart error:', error);
    }

    // Revenue Chart - REMOVED, replaced with Best Selling Products table
    console.log('✅ All charts initialized successfully');
    
    // Update Best Selling Products table
    updateBestSellingTable();
}

// Update Best Selling Products Table
function updateBestSellingTable() {
    const tableBody = document.getElementById('bestSellingTableBody');
    if (!tableBody) return;
    
    // Sample data - in real implementation, this would come from your database
    const salesData = [
        {
            product: '🥚 Quail Eggs',
            quantity: Math.floor(Math.random() * 50) + 20,
            unit: 'trays',
            price: 80.00,
            percentage: 0
        },
        {
            product: '🐦 Live Quail', 
            quantity: Math.floor(Math.random() * 20) + 5,
            unit: 'pcs',
            price: 180.00,
            percentage: 0
        },
        {
            product: '🍗 Dressed Quail',
            quantity: Math.floor(Math.random() * 15) + 3,
            unit: 'pcs', 
            price: 250.00,
            percentage: 0
        }
    ];
    
    // Calculate totals and percentages
    let totalRevenue = 0;
    salesData.forEach(item => {
        item.total = item.quantity * item.price;
        totalRevenue += item.total;
    });
    
    salesData.forEach(item => {
        item.percentage = totalRevenue > 0 ? Math.round((item.total / totalRevenue) * 100) : 0;
    });
    
    // Sort by total revenue (descending)
    salesData.sort((a, b) => b.total - a.total);
    
    // Update table
    tableBody.innerHTML = salesData.map((item, index) => `
        <tr ${index % 2 === 1 ? 'style="background: #f9f9f9;"' : ''}>
            <td style="padding: 1rem; font-size: 0.9rem; color: #000;">${item.product}</td>
            <td style="padding: 1rem; font-size: 0.9rem; color: #000;">${item.quantity} ${item.unit}</td>
            <td style="padding: 1rem; font-size: 0.9rem; color: #000;">₱${item.price.toFixed(2)}</td>
            <td style="padding: 1rem; font-size: 0.9rem; color: #000; font-weight: 700;">₱${item.total.toFixed(2)}</td>
            <td style="padding: 1rem; font-size: 0.9rem; color: #000;">${item.percentage}%</td>
        </tr>
    `).join('');
}
    
// Update Analytics Function
function updateAnalytics() {
    console.log('🔄 Update Analytics button clicked!');
    
    const fromDate = document.getElementById('analyticsFromDate');
    const toDate = document.getElementById('analyticsToDate');
    
    if (!fromDate || !toDate) {
        console.error('Date inputs not found!');
        alert('❌ Error: Date inputs not found!');
        return;
    }
    
    console.log('Date range:', fromDate.value, 'to', toDate.value);
    
    // Show loading state
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = '🔄 Updating...';
    button.disabled = true;
    
    // Simulate data loading and update
    setTimeout(() => {
        // Update analytics cards with new random data
        const success = updateAnalyticsCards();
        
        // Update Best Selling Products table
        updateBestSellingTable();
        
        // Reset button
        button.textContent = originalText;
        button.disabled = false;
        
        if (success) {
            console.log('✅ Analytics update completed!');
            alert('✅ Analytics updated successfully!');
        } else {
            console.error('❌ Failed to update analytics cards');
            alert('❌ Failed to update analytics!');
        }
    }, 1000);
}

// Update Analytics Cards
function updateAnalyticsCards() {
    console.log('Updating analytics cards...');
    
    // Generate random sample data
    const eggsCollected = Math.floor(Math.random() * 1000) + 500;
    const totalRevenue = Math.floor(Math.random() * 50000) + 10000;
    const activeDays = Math.floor(Math.random() * 30) + 1;
    
    console.log('Generated data:', { eggsCollected, totalRevenue, activeDays });
    
    // Update the display
    const analyticsEggsCollected = document.getElementById('analyticsEggsCollected');
    const analyticsTotalRevenue = document.getElementById('analyticsTotalRevenue');
    const analyticsActiveDays = document.getElementById('analyticsActiveDays');
    
    console.log('Found elements:', {
        eggs: !!analyticsEggsCollected,
        revenue: !!analyticsTotalRevenue,
        days: !!analyticsActiveDays
    });
    
    let success = true;
    
    if (analyticsEggsCollected) {
        analyticsEggsCollected.textContent = eggsCollected + ' eggs';
        console.log('Updated eggs to:', eggsCollected + ' eggs');
    } else {
        console.error('analyticsEggsCollected element not found!');
        success = false;
    }
    
    if (analyticsTotalRevenue) {
        analyticsTotalRevenue.textContent = '₱' + totalRevenue.toLocaleString() + '.00';
        console.log('Updated revenue to:', '₱' + totalRevenue.toLocaleString() + '.00');
    } else {
        console.error('analyticsTotalRevenue element not found!');
        success = false;
    }
    
    if (analyticsActiveDays) {
        analyticsActiveDays.textContent = activeDays + ' days';
        console.log('Updated days to:', activeDays + ' days');
    } else {
        console.error('analyticsActiveDays element not found!');
        success = false;
    }
    
    return success;
}

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
</script>
@endsection
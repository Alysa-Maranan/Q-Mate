@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    body {
        background: linear-gradient(135deg, #f5f0eb 0%, #efe8e4 50%, #e8dcd6 100%);
        min-height: 100vh;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        font-size: 1rem;
        color: #4e342e;
    }

    .inventory-container {
        max-width: 100%;
        margin: 0 auto;
        padding: 5rem 3rem 2.5rem;
        min-height: 100vh;
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

    /* Section Card */
    .section-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12);
        border: 1px solid rgba(161, 136, 127, 0.1);
        margin-bottom: 2rem;
    }
    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #6d4c41;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #d7ccc8;
    }

    /* Info Grid */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin: 1.5rem 0;
    }
    .info-card {
        background: #efebe9;
        border-radius: 16px;
        padding: 1.5rem;
        border: 2px solid #d7ccc8;
        transition: all 0.3s ease;
    }
    .info-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(161, 136, 127, 0.2);
    }
    .info-card-icon {
        font-size: 2rem;
        margin-bottom: 0.75rem;
    }
    .info-card-title {
        font-size: 1rem;
        font-weight: 700;
        color: #6d4c41;
        margin-bottom: 0.5rem;
    }
    .info-card-text {
        color: #795548;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    /* Form */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    .form-group {
        margin-bottom: 1rem;
    }
    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #6d4c41;
        margin-bottom: 0.5rem;
    }
    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1.5px solid #d7ccc8;
        border-radius: 10px;
        font-size: 0.95rem;
        font-family: inherit;
        background: #f9fafb;
        color: #4e342e;
        transition: all 0.3s ease;
    }
    .form-input:focus {
        outline: none;
        border-color: #a1887f;
        background: white;
    }

    /* Buttons */
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.95rem;
        font-family: inherit;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
    }
    .btn-primary {
        background: linear-gradient(135deg, #a1887f 0%, #8d6e63 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(141, 110, 99, 0.3);
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(141, 110, 99, 0.4);
    }

    /* Hidden category content */
    .category-content {
        display: none;
    }
    .category-content.active {
        display: block;
    }

    /* Table */
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    .table th {
        background: #efebe9;
        padding: 1rem;
        text-align: left;
        font-weight: 700;
        font-size: 0.875rem;
        color: #6d4c41;
        border-bottom: 2px solid #d7ccc8;
    }
    .table td {
        padding: 1rem;
        border-bottom: 1px solid #d7ccc8;
        color: #4e342e;
        font-size: 0.95rem;
    }
    .table tr:hover {
        background: #f5f0eb;
    }

    /* Action Buttons */
    .action-btn {
        padding: 0.5rem 0.875rem;
        border: none;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        margin-right: 6px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .action-btn:hover { transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
    .btn-update { background: linear-gradient(135deg, #6d4c41 0%, #5d4037 100%); color: white; }
    .btn-update:hover { background: linear-gradient(135deg, #5d4037 0%, #4e342e 100%); }
    .btn-delete { background: linear-gradient(135deg, #8d6e63 0%, #795548 100%); color: white; }
    .btn-delete:hover { background: linear-gradient(135deg, #795548 0%, #6d4c41 100%); }
</style>

@include('components.navbar')

<div id="main-content-wrap" class="sidebar-content-wrap">
<div class="inventory-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Quail Farm Inventory & Sales</h1>
    </div>

    <!-- Category Tabs -->
    <div class="category-tabs">
        <button class="category-tab active" onclick="showCategory('collect')">
            Collect & Inspect Eggs
        </button>
        <button class="category-tab" onclick="showCategory('sales')">
            Record Sales
        </button>
    </div>

    <!-- COLLECT & INSPECT EGGS Category -->
    <div id="collect" class="category-content active">
        <div class="section-card">
            <h2 class="section-title">Collect & Inspect Eggs from Cages/Pens</h2>
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-card-icon">🥚</div>
                    <div class="info-card-title">Cage/Pen 1</div>
                    <div class="info-card-text">Ready for collection</div>
                </div>
                <div class="info-card">
                    <div class="info-card-icon">🥚</div>
                    <div class="info-card-title">Cage/Pen 2</div>
                    <div class="info-card-text">Ready for collection</div>
                </div>
                <div class="info-card">
                    <div class="info-card-icon">🥚</div>
                    <div class="info-card-title">Cage/Pen 3</div>
                    <div class="info-card-text">Ready for collection</div>
                </div>
                <div class="info-card">
                    <div class="info-card-icon">🥚</div>
                    <div class="info-card-title">Cage/Pen 4</div>
                    <div class="info-card-text">Ready for collection</div>
                </div>
            </div>
            <form>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Select Cage/Pen</label>
                        <select class="form-input" id="collectCagePen">
                            <option value="1">Cage/Pen 1</option>
                            <option value="2">Cage/Pen 2</option>
                            <option value="3">Cage/Pen 3</option>
                            <option value="4">Cage/Pen 4</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Total Eggs Collected</label>
                        <input type="number" class="form-input" id="collectEggs" placeholder="Enter total eggs" min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Good Eggs (No Crack)</label>
                        <input type="number" class="form-input" id="collectGoodEggs" placeholder="Number of good eggs" min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Cracked/Damaged Eggs</label>
                        <input type="number" class="form-input" id="collectCrackedEggs" placeholder="Number of cracked eggs" min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Egg Size Classification</label>
                        <select class="form-input" id="collectEggSize">
                            <option value="mixed">Mixed Sizes</option>
                            <option value="small">Small (15-18g)</option>
                            <option value="medium">Medium (18-22g)</option>
                            <option value="large">Large (22g+)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Feed Brand Used</label>
                        <select class="form-input" id="collectFeedBrand">
                            <option value="quail_layer_smash">Quail Layer Smash</option>
                            <option value="jedstar">Jedstar Feeds</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Collection Time</label>
                        <input type="time" class="form-input" id="collectTime">
                    </div>
                </div>
                <button type="button" class="btn btn-primary" style="margin-top: 1rem;" onclick="recordCollection()">Record Collection & Inspection</button>
            </form>
        </div>

        <!-- Collection Records Table -->
        <div class="section-card">
            <h2 class="section-title">Collection & Inspection Records</h2>
            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f5f5f5;">
                            <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Date</th>
                            <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Time</th>
                            <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Cage/Pen</th>
                            <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Total Collected</th>
                            <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Good Eggs</th>
                            <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Cracked Eggs</th>
                            <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Quality Rate</th>
                            <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Status</th>
                            <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="collectionTableBody">
                        @php
                            $collections = \App\Models\EggCollection::orderBy('created_at', 'desc')->limit(20)->get();
                        @endphp
                        @forelse($collections as $collection)
                            <tr style="border-bottom: 1px solid #efebe9;">
                                <td style="padding: 1rem; font-size: 0.88rem; color: #000;">{{ $collection->created_at->format('M d, Y') }}</td>
                                <td style="padding: 1rem; font-size: 0.88rem; color: #000;">{{ $collection->collection_time ? \Carbon\Carbon::parse($collection->collection_time)->format('h:i A') : $collection->created_at->format('h:i A') }}</td>
                                <td style="padding: 1rem; font-size: 0.88rem; color: #000;">Cage/Pen {{ $collection->cage_pen }}</td>
                                <td style="padding: 1rem; font-size: 0.88rem; color: #000;">{{ $collection->total_eggs }} eggs</td>
                                <td style="padding: 1rem; font-size: 0.88rem; color: #000;">{{ $collection->good_eggs }} eggs</td>
                                <td style="padding: 1rem; font-size: 0.88rem; color: #000;">{{ $collection->cracked_eggs }} eggs</td>
                                <td style="padding: 1rem; font-size: 0.88rem; color: #000; font-weight: 700;">{{ $collection->quality_rate }}%</td>
                                <td style="padding: 1rem;">
                                    <span style="display: inline-block; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; background: #e8f5e9; color: #2e7d32;">
                                        ✅ Recorded
                                    </span>
                                </td>
                                <td style="padding: 1rem;">
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button class="action-btn btn-update" onclick="editCollection({{ $collection->id }})" title="Edit Record">✏️</button>
                                        <button class="action-btn btn-delete" onclick="deleteCollection({{ $collection->id }})" title="Delete Record">🗑️</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="padding: 2rem; text-align: center; color: #a1887f;">
                                    No collections recorded yet. Start collecting eggs to see records here.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- RECORD SALES Category -->
    <div id="sales" class="category-content">
        <div class="section-card">
            <h2 class="section-title">Record Daily Sales</h2>
            <form>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Product Type</label>
                        <select class="form-input" id="salesProductType" onchange="updateSalesPrice()">
                            <option value="eggs">Quail Eggs (₱80/tray)</option>
                            <option value="live_quail">Live Quail (₱180/pc)</option>
                            <option value="dressed_quail">Dressed Quail (₱250/pc)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Quantity Sold</label>
                        <input type="number" class="form-input" id="salesQuantity" placeholder="Enter quantity" min="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Price per Unit</label>
                        <input type="number" class="form-input" id="salesPrice" placeholder="₱80.00" value="80">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Customer Name (Optional)</label>
                        <input type="text" class="form-input" id="customerName" placeholder="Customer name">
                    </div>
                </div>
                <button type="button" class="btn btn-primary" style="margin-top: 1rem;" onclick="recordSale()">Record Sale</button>
            </form>

            <div style="margin-top: 2rem;">
                <h3 style="color: #6d4c41; margin: 0 0 1rem 0;">Sales Records</h3>
                <div style="overflow-x: auto;">
                    <table class="table" style="width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden;">
                        <thead>
                            <tr style="background: #f5f5f5;">
                                <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Date</th>
                                <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Product</th>
                                <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Quantity</th>
                                <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Price</th>
                                <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Total</th>
                            </tr>
                        </thead>
                        <tbody id="salesRecordsBody">
                            <tr>
                                <td colspan="5" style="padding: 2rem; text-align: center; color: #a1887f;">No sales recorded yet. Start recording sales to see records here.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
const salesRecords = [];

// Initialize form with current time
document.addEventListener('DOMContentLoaded', function() {
    const now = new Date();
    const currentTime = now.toTimeString().slice(0, 5);
    const collectTimeInput = document.getElementById('collectTime');
    if (collectTimeInput) {
        collectTimeInput.value = currentTime;
    }

    loadSalesRecords();
    updateSalesPrice();
});

// Show category function
function showCategory(categoryId) {
    // Hide all categories
    document.querySelectorAll('.category-content').forEach(content => {
        content.classList.remove('active');
    });
    
    // Remove active class from all tabs
    document.querySelectorAll('.category-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Show selected category
    document.getElementById(categoryId).classList.add('active');
    
    // Add active class to clicked tab
    if (event && event.currentTarget) {
        event.currentTarget.classList.add('active');
    } else {
        // If called programmatically, find and activate the corresponding tab
        document.querySelectorAll('.category-tab').forEach(tab => {
            if (tab.textContent.toLowerCase().includes(categoryId.toLowerCase()) || 
                tab.getAttribute('onclick').includes(categoryId)) {
                tab.classList.add('active');
            }
        });
    }
    
    // Save active tab to localStorage
    localStorage.setItem('inventoryFixedActiveTab', categoryId);
}

// Restore active tab from localStorage on page load for inventory_fixed
(function() {
    const savedTab = localStorage.getItem('inventoryFixedActiveTab');
    
    if (savedTab && document.getElementById(savedTab)) {
        // Create a fake event object for showCategory function
        const fakeEvent = {
            currentTarget: document.querySelector(`[onclick*="${savedTab}"]`)
        };
        if (fakeEvent.currentTarget) {
            const oldEvent = window.event;
            window.event = fakeEvent;
            showCategory(savedTab);
            window.event = oldEvent;
        }
    }
})();

function updateSalesPrice() {
    const select = document.getElementById('salesProductType');
    const priceInput = document.getElementById('salesPrice');

    if (!select || !priceInput) {
        return;
    }

    const prices = {
        eggs: 80,
        live_quail: 180,
        dressed_quail: 250
    };

    priceInput.value = prices[select.value] || 0;
}

function loadSalesRecords() {
    try {
        const saved = localStorage.getItem('salesRecords') || localStorage.getItem('inventoryFixedSalesRecords');
        if (saved) {
            const parsed = JSON.parse(saved);
            if (Array.isArray(parsed)) {
                salesRecords.splice(0, salesRecords.length, ...parsed);
                localStorage.setItem('salesRecords', JSON.stringify(salesRecords));
            }
        }
    } catch (error) {
        console.error('Error loading sales records:', error);
    }

    renderSalesRecords();
}

function saveSalesRecords() {
    localStorage.setItem('salesRecords', JSON.stringify(salesRecords));
    localStorage.setItem('inventoryFixedSalesRecords', JSON.stringify(salesRecords));
}

function renderSalesRecords() {
    const tbody = document.getElementById('salesRecordsBody');
    if (!tbody) {
        return;
    }

    if (salesRecords.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="padding: 2rem; text-align: center; color: #a1887f;">No sales recorded yet. Start recording sales to see records here.</td></tr>';
        return;
    }

    const productNames = {
        eggs: 'Quail Eggs',
        live_quail: 'Live Quail',
        dressed_quail: 'Dressed Quail'
    };

    tbody.innerHTML = salesRecords.map(record => `
        <tr style="border-bottom: 1px solid #efebe9;">
            <td style="padding: 1rem; color: #000; font-size: 0.9rem;">${record.date}</td>
            <td style="padding: 1rem; color: #000; font-size: 0.9rem;">${productNames[record.productType] || record.productType}</td>
            <td style="padding: 1rem; color: #000; font-size: 0.9rem;">${record.quantity}</td>
            <td style="padding: 1rem; color: #000; font-size: 0.9rem;">₱${parseFloat(record.price).toFixed(2)}</td>
            <td style="padding: 1rem; color: #000; font-size: 0.9rem; font-weight: 700;">₱${parseFloat(record.total).toFixed(2)}</td>
        </tr>
    `).join('');
}

// Record collection function
function recordCollection() {
    const cagePen = document.getElementById('collectCagePen').value;
    const totalEggs = parseInt(document.getElementById('collectEggs').value) || 0;
    const goodEggs = parseInt(document.getElementById('collectGoodEggs').value) || 0;
    const crackedEggs = parseInt(document.getElementById('collectCrackedEggs').value) || 0;
    const time = document.getElementById('collectTime').value;
    
    if (totalEggs <= 0) {
        alert('Please enter a valid number of total eggs.');
        return;
    }
    
    if (goodEggs + crackedEggs !== totalEggs) {
        alert('Good eggs + Cracked eggs must equal Total eggs collected.');
        return;
    }
    
    // Save to database via API
    fetch('/api/egg-collections', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            cage_pen: cagePen,
            total_eggs: totalEggs,
            good_eggs: goodEggs,
            cracked_eggs: crackedEggs,
            collection_time: time
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Collection recorded successfully!');
            location.reload(); // Refresh to show new record
        } else {
            alert('Error: ' + (result.message || 'Failed to save collection'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving collection');
    });
}

// Record sale function
function recordSale() {
    const productType = document.getElementById('salesProductType').value;
    const quantity = parseInt(document.getElementById('salesQuantity').value) || 0;
    const price = parseFloat(document.getElementById('salesPrice').value) || 0;
    const customerName = document.getElementById('customerName').value;
    
    if (quantity <= 0) {
        alert('Please enter a valid quantity.');
        return;
    }

    const now = new Date();
    const saleRecord = {
        date: now.toLocaleDateString(),
        productType: productType,
        quantity: quantity,
        price: price,
        total: quantity * price,
        customerName: customerName,
        timestamp: now.getTime()
    };

    salesRecords.unshift(saleRecord);
    saveSalesRecords();
    renderSalesRecords();
    
    // Save to database via API
    fetch('/api/sales', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            product_type: productType,
            quantity: quantity,
            price: price,
            customer_name: customerName
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Sale recorded successfully!');
            // Reset form
            document.getElementById('salesQuantity').value = '';
            document.getElementById('customerName').value = '';
            updateSalesPrice();
        } else {
            alert('Sale saved locally, but server sync failed: ' + (result.message || 'Failed to save sale'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Sale saved locally, but server sync failed. Please check your connection.');
    });
}

// Edit collection function
function editCollection(id) {
    // Implement edit functionality
    console.log('Edit collection:', id);
}

// Delete collection function
function deleteCollection(id) {
    if (confirm('Are you sure you want to delete this collection record?')) {
        fetch(`/api/egg-collections/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                location.reload();
            } else {
                alert('Error deleting collection');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting collection');
        });
    }
}
</script>

@endsection
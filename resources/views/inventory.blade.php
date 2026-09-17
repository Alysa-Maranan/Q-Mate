@extends('layouts.app')

@section('content')
@include('components.sidebar')

<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    body {
        background: linear-gradient(135deg, #f5f0eb 0%, #efe8e4 50%, #e8dcd6 100%);
        min-height: 100vh;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        font-size: 1rem;
        color: #4e342e;
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
    .sidebar-content-wrap.expanded .inventory-container {
        max-width: 1600px;
        margin: 0 auto;
    }

    @media (max-width: 768px) {
        .sidebar-content-wrap {
            margin-left: 0 !important;
            padding-top: 70px;
        }
        .inventory-container { padding: 1.5rem 1rem; }
    }

    .inventory-container {
        max-width: 100%;
        margin: 0 auto;
        padding: 2rem;
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
    .category-icon {
        font-size: 1rem;
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

    /* Product Management */
    .product-management-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        border: 2px solid #e8dcd6;
        transition: all 0.3s ease;
        position: relative;
    }
    .product-management-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(109,76,65,0.15); }
    .product-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; }
    .product-name { font-size: 1rem; font-weight: 700; color: #4e342e; margin-bottom: 0.25rem; }
    .product-unit { font-size: 0.85rem; color: #8d6e63; }
    .stock-badge { padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; background: #4caf50; color: white; }
    .stock-badge.low { background: #ff9800; }
    .stock-badge.out { background: #f44336; }
    .product-controls { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-top: 1rem; }
    .price-display {
        background: linear-gradient(135deg, #6d4c41 0%, #5d4037 100%);
        color: white; padding: 0.75rem; border-radius: 10px;
        text-align: center; font-weight: 700; font-size: 0.95rem; cursor: pointer; transition: all 0.2s;
    }
    .price-display:hover { background: linear-gradient(135deg, #5d4037 0%, #4e342e 100%); transform: translateY(-1px); }
    .stock-display {
        background: #f5f0eb; border: 2px solid #d7ccc8; padding: 0.75rem; border-radius: 10px;
        text-align: center; font-weight: 600; font-size: 0.95rem; color: #6d4c41; cursor: pointer; transition: all 0.2s;
    }
    .stock-display:hover { background: #efebe9; border-color: #a1887f; }

    /* Edit Modals */
    .edit-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center; }
    .edit-modal.show { display: flex; }
    .edit-modal-content { background: white; padding: 2rem; border-radius: 16px; width: 90%; max-width: 400px; box-shadow: 0 20px 60px rgba(109,76,65,0.3); }
    .edit-modal-header { font-size: 1.1rem; font-weight: 700; color: #4e342e; margin-bottom: 1rem; text-align: center; }
    .edit-modal-input {
        width: 100%; padding: 0.75rem; border: 1.5px solid #d7ccc8; border-radius: 8px;
        font-size: 0.95rem; font-family: inherit; color: #4e342e; margin-bottom: 1rem; box-sizing: border-box;
    }
    .edit-modal-buttons { display: flex; gap: 0.5rem; }
    .modal-btn { padding: 0.75rem 1.5rem; border: none; border-radius: 8px; font-weight: 600; font-size: 0.95rem; font-family: inherit; cursor: pointer; flex: 1; transition: all 0.2s; }
    .modal-btn-primary { background: #6d4c41; color: white; }
    .modal-btn-primary:hover { background: #5d4037; }
    .modal-btn-secondary { background: #e0e0e0; color: #666; }
    .modal-btn-secondary:hover { background: #d0d0d0; }

    /* Update Modal Form */
    .update-form-group { margin-bottom: 1rem; text-align: left; }
    .update-form-label { display: block; font-size: 0.875rem; font-weight: 600; color: #6d4c41; margin-bottom: 0.5rem; }
    .update-form-input {
        width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d7ccc8; border-radius: 10px;
        font-size: 0.95rem; font-family: inherit; color: #4e342e; background: #f9fafb; transition: all 0.3s ease; box-sizing: border-box;
    }

    /* Custom Confirmation Modal */
    .confirm-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    .confirm-modal-overlay.show {
        display: flex;
    }
    .confirm-modal {
        background: #fff;
        border-radius: 16px;
        padding: 2rem;
        max-width: 360px;
        width: 90%;
        text-align: center;
        box-shadow: 0 20px 60px rgba(109, 76, 65, 0.3);
        animation: modalSlideIn 0.2s ease;
    }
    @keyframes modalSlideIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .confirm-modal-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    .confirm-modal-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #4e342e;
        margin-bottom: 0.5rem;
    }
    .confirm-modal-message {
        font-size: 0.95rem;
        color: #6d4c41;
        margin-bottom: 1.5rem;
    }
    .confirm-modal-buttons {
        display: flex;
        gap: 0.75rem;
        justify-content: center;
    }
    .confirm-modal-btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
    }
    .confirm-modal-btn.cancel {
        background: #efebe9;
        color: #6d4c41;
    }
    .confirm-modal-btn.cancel:hover {
        background: #d7ccc8;
    }
    .confirm-modal-btn.confirm {
        background: linear-gradient(135deg, #6d4c41 0%, #4e342e 100%);
        color: #fff;
    }
    .confirm-modal-btn.confirm:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(109, 76, 65, 0.3);
    }
    

</style>

<!-- Temperature & Humidity Modal -->
<div class="confirm-modal-overlay" id="temperatureHumidityModal">
    <div class="confirm-modal" style="max-width: 600px; width: 90%;">
        <button onclick="closeTemperatureHumidity()" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6d4c41;">&times;</button>
        
        <div class="confirm-modal-title">Temperature & Humidity Monitor</div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin: 1.5rem 0;">
            <div style="background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%); border-radius: 16px; padding: 1.5rem; text-align: center; border: 2px solid #ef5350;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;"></div>
                <div style="font-size: 0.8rem; color: #c62828; font-weight: 700; text-transform: uppercase; margin-bottom: 0.5rem;">Temperature</div>
                <div id="current-temp" style="font-size: 2.5rem; font-weight: 800; color: #d32f2f;">25C</div>
                <div style="font-size: 0.75rem; color: #e57373; margin-top: 0.25rem;">Current</div>
            </div>
            
            <div style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-radius: 16px; padding: 1.5rem; text-align: center; border: 2px solid #2196f3;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;"></div>
                <div style="font-size: 0.8rem; color: #1565c0; font-weight: 700; text-transform: uppercase; margin-bottom: 0.5rem;">Humidity</div>
                <div id="current-humidity" style="font-size: 2.5rem; font-weight: 800; color: #1976d2;">65%</div>
                <div style="font-size: 0.75rem; color: #64b5f6; margin-top: 0.25rem;">Current</div>
            </div>
        </div>
        
        <div style="background: #f5f0eb; border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                <span style="font-size: 1.25rem;"></span>
                <span style="font-weight: 700; color: #6d4c41;">Status & Recommendations</span>
            </div>
            <div id="temp-humidity-advice" style="color: #5d4037; font-size: 0.95rem; line-height: 1.5;">Temperature and humidity levels are optimal for quail farming.</div>
        </div>
        
        <div style="background: #efebe9; border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                <span style="font-size: 1.25rem;"></span>
                <span style="font-weight: 700; color: #6d4c41;">Optimal Ranges for Quail</span>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.85rem; color: #5d4037;">
                <div>
                    <strong>Temperature:</strong><br>
                    Adult Quail: 18-24C<br>
                    Chicks: 35-37C
                </div>
                <div>
                    <strong>Humidity:</strong><br>
                    Optimal: 50-70%<br>
                    Breeding: 60-65%
                </div>
            </div>
        </div>
        
        <div class="confirm-modal-buttons">
            <button class="confirm-modal-btn confirm" onclick="closeTemperatureHumidity()">Close</button>
        </div>
    </div>
</div>



<!-- Custom Confirmation Modal -->
<div class="confirm-modal-overlay" id="confirmModal">
    <div class="confirm-modal">
        <div class="confirm-modal-icon" id="confirmModalIcon"></div>
        <div class="confirm-modal-title" id="confirmModalTitle">Confirm Action</div>
        <div class="confirm-modal-message" id="confirmModalMessage">Are you sure?</div>
        <div class="confirm-modal-buttons">
            <button class="confirm-modal-btn cancel" onclick="closeConfirmModal()">Cancel</button>
            <button class="confirm-modal-btn confirm" id="confirmModalYes">Yes</button>
        </div>
    </div>
</div>

<!-- Update Record Modal -->
<div class="confirm-modal-overlay" id="updateModal">
    <div class="confirm-modal" style="max-width: 500px; width: 90%;">
        <div class="confirm-modal-icon" id="updateModalIcon"></div>
        <div class="confirm-modal-title" id="updateModalTitle">Update Record</div>
        <div id="updateModalContent" style="margin: 1.5rem 0;">
            <!-- Dynamic form content will be inserted here -->
        </div>
        <div class="confirm-modal-buttons">
            <button class="confirm-modal-btn cancel" onclick="closeUpdateModal()">Cancel</button>
            <button class="confirm-modal-btn confirm" id="updateModalSave" onclick="saveUpdateModal()">Save Changes</button>
        </div>
    </div>
</div>


<div id="main-content-wrap" class="sidebar-content-wrap">
<div class="inventory-container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Quail Farm Inventory & Sales</h1>
        <p style="margin: 0.5rem 0 0; color: #8d6e63; font-size: 0.95rem;">Record egg collections, manage your daily sales and monitor your farm stocks â€” all in one place.</p>
    </div>

    <!-- Category Tabs -->
    <div class="category-tabs">
        <button class="category-tab active" onclick="showCategory('collect')">
            <span class="category-icon"></span>Collect & Inspect Eggs
        </button>
        <button class="category-tab" onclick="showCategory('quail-management')">
            <span class="category-icon"></span>Quail Management
        </button>
        <button class="category-tab" onclick="showCategory('sales')">
            <span class="category-icon"></span>Record Sales
        </button>
        <button class="category-tab" onclick="showCategory('analytics')">
            <span class="category-icon"></span>Analytics & Reports
        </button>
    </div>

    <!-- COLLECT & INSPECT EGGS Category -->
    <div id="collect" class="category-content active">
        <div class="section-card">
            <h2 class="section-title">Collect & Inspect Eggs from Cages/Pens</h2>
            <form>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Select Cage/Pen</label>
                        <select class="form-input" id="collectCagePen">
                            <option value="1">Cage/Pen 1</option>
                            <option value="2">Cage/Pen 2</option>
                            <option value="3">Cage/Pen 3</option>
                            <option value="4">Cage/Pen 4</option>
                            <option value="5">Cage/Pen 5</option>
                            <option value="6">Cage/Pen 6</option>
                            <option value="7">Cage/Pen 7</option>
                            <option value="8">Cage/Pen 8</option>
                            <option value="9">Cage/Pen 9</option>
                            <option value="10">Cage/Pen 10</option>
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
                <button type="button" class="btn btn-primary" style="margin-top: 1rem;" onclick="confirmRecordCollection()">Record Collection & Inspection</button>
            </form>
        </div>

        <!-- Collection Records Table -->
        <div class="section-card">
            <h2 class="section-title">Collection & Inspection Records</h2>
            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden;">
                    <thead>
                        <tr style="background: linear-gradient(135deg, #6d4c41, #8d6e63); color: white;">
                            <th style="padding: 1rem; text-align: left; font-weight: 700; font-size: 0.85rem;">Date</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 700; font-size: 0.85rem;">Time</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 700; font-size: 0.85rem;">Cage/Pen</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 700; font-size: 0.85rem;">Total Eggs</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 700; font-size: 0.85rem;">Good Eggs</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 700; font-size: 0.85rem;">Cracked</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 700; font-size: 0.85rem;">Size</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 700; font-size: 0.85rem;">Feed Brand</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 700; font-size: 0.85rem;">Status</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 700; font-size: 0.85rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="collectionTableBody">
                        @php
                            $collections = \App\Models\EggCollection::orderBy('created_at', 'desc')->limit(20)->get();
                        @endphp
                        @forelse($collections as $collection)
                            @php
                                $eggSizeDisplay = [
                                    'mixed' => 'Mixed Sizes',
                                    'small' => 'Small (15-18g)',
                                    'medium' => 'Medium (18-22g)',
                                    'large' => 'Large (22g+)'
                                ];
                                $feedBrandDisplay = [
                                    'quail_layer_smash' => 'Quail Layer Smash',
                                    'jedstar' => 'Jedstar Feeds'
                                ];
                            @endphp
                            <tr style="border-bottom: 1px solid #d7ccc8;">
                                <td style="padding: 1rem; font-size: 0.88rem; color: #6d4c41; font-weight: 600;">{{ $collection->created_at->format('M d, Y') }}</td>
                                <td style="padding: 1rem; font-size: 0.88rem; color: #8d6e63;">{{ $collection->collection_time ? \Carbon\Carbon::parse($collection->collection_time)->format('h:i A') : $collection->created_at->format('h:i A') }}</td>
                                <td style="padding: 1rem; font-size: 0.88rem; color: #6d4c41; text-align: center; font-weight: 600;">{{ $collection->cage_pen }}</td>
                                <td style="padding: 1rem; font-size: 0.88rem; color: #6d4c41; text-align: center; font-weight: 700;">{{ $collection->total_eggs }}</td>
                                <td style="padding: 1rem; font-size: 0.88rem; color: #4caf50; text-align: center; font-weight: 700;">{{ $collection->good_eggs }}</td>
                                <td style="padding: 1rem; font-size: 0.88rem; color: #d32f2f; text-align: center; font-weight: 700;">{{ $collection->cracked_eggs }}</td>
                                <td style="padding: 1rem; font-size: 0.88rem; color: #8d6e63; text-align: center;">{{ $eggSizeDisplay[$collection->egg_size] ?? 'Mixed' }}</td>
                                <td style="padding: 1rem; font-size: 0.88rem; color: #8d6e63;">{{ $feedBrandDisplay[$collection->feed_brand] ?? 'Quail Layer Smash' }}</td>
                                <td style="padding: 1rem; text-align: center;">
                                    <span style="display: inline-block; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; background: #e8f5e9; color: #2e7d32;">Recorded</span>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                        <button class="action-btn btn-update" onclick="editCollectionFromDB({{ $collection->id }})" title="Edit" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">Edit</button>
                                        <button class="action-btn btn-delete" onclick="deleteCollectionFromDB({{ $collection->id }})" title="Delete" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr style="border-bottom: 1px solid #d7ccc8;">
                                <td style="padding: 1rem; font-size: 0.88rem; color: #6d4c41; font-weight: 600;">Apr 02, 2026</td>
                                <td style="padding: 1rem; font-size: 0.88rem; color: #8d6e63;">08:30 AM</td>
                                <td style="padding: 1rem; font-size: 0.88rem; color: #6d4c41; text-align: center; font-weight: 600;">1</td>
                                <td style="padding: 1rem; font-size: 0.88rem; color: #6d4c41; text-align: center; font-weight: 700;">28</td>
                                <td style="padding: 1rem; font-size: 0.88rem; color: #4caf50; text-align: center; font-weight: 700;">26</td>
                                <td style="padding: 1rem; font-size: 0.88rem; color: #d32f2f; text-align: center; font-weight: 700;">2</td>
                                <td style="padding: 1rem; font-size: 0.88rem; color: #8d6e63; text-align: center;">Medium (18-22g)</td>
                                <td style="padding: 1rem; font-size: 0.88rem; color: #8d6e63;">Quail Layer Smash</td>
                                <td style="padding: 1rem; text-align: center;">
                                    <span style="display: inline-block; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; background: #e8f5e9; color: #2e7d32;">Recorded</span>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                        <button class="action-btn btn-update" onclick="alert('Sample data - no edit available')" title="Edit" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">Edit</button>
                                        <button class="action-btn btn-delete" onclick="alert('Sample data - no delete available')" title="Delete" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>




    <div id="sales" class="category-content">
        <div class="section-card">
            <h2 class="section-title">Record Daily Sales</h2>
            <div class="info-grid">
                <!-- Info cards removed for cleaner interface -->
            </div>
            <form>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Product Type</label>
                        <select class="form-input" id="salesProductType" onchange="updateSalesPrice()">
                            <option value="eggs" data-price="160">Quail Eggs (₱160/tray)</option>
                            <option value="live_quail" data-price="180">Live Quail (₱180/pc)</option>
                            <option value="dressed_quail" data-price="250">Dressed Quail (₱250/pc)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Quantity Sold</label>
                        <input type="number" class="form-input" id="salesQuantity" placeholder="Enter quantity" min="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Price per Unit</label>
                        <input type="number" class="form-input" id="salesPrice" placeholder="₱0.00" value="">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Customer Name (Optional)</label>
                        <input type="text" class="form-input" placeholder="Customer name">
                    </div>
                </div>
                <div style="margin-top: 1rem; padding: 1rem; background: #f5f0eb; border-radius: 10px; display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #6d4c41; font-weight: 600;">Total Amount:</span>
                    <span id="salesTotalAmount" style="font-size: 1.25rem; font-weight: 800; color: #4e342e;">₱0.00</span>
                </div>
                <button type="button" class="btn btn-primary" style="margin-top: 1rem;" onclick="confirmRecordSale()">Record Sale</button>
            </form>

            <!-- Sales Records Table -->
            <div class="mt-5">
                <h5>Sales Records</h5>
                <div style="overflow-x: auto;">
                    <table class="table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f5f5f5;">
                                <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Date</th>
                                <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Time</th>
                                <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Product</th>
                                <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Quantity</th>
                                <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Price</th>
                                <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Total</th>
                                <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Status</th>
                                <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="salesRecordsBody">
                            <tr>
                              <td colspan="8" style="padding: 2rem; text-align: center; color: #a1887f;">
                                    No sales records yet. Start recording sales to see records here.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- QUAIL MANAGEMENT Category -->
    <div id="quail-management" class="category-content">
        <div class="section-card">
            <h2 class="section-title">Quail Inventory Management</h2>
            <p style="color: #8d6e63; margin-bottom: 1.5rem;">Track and manage your live quails and record mortality count.</p>
            
            <div style="background: #f5f0eb; border-radius: 16px; padding: 1.5rem; margin-bottom: 2rem; border: 2px solid #d7ccc8;">
                <h3 style="color: #6d4c41; margin: 0 0 1.5rem 0;">Update Quail Count</h3>
                <form method="POST" action="{{ route('settings.farm-info') }}" id="quailManagementForm">
                    @csrf
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #6d4c41; margin-bottom: 0.5rem;">Date</label>
                            <input type="date" name="quail_count_date" id="quailCountDate" value="{{ date('Y-m-d') }}" style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d7ccc8; border-radius: 10px; font-size: 0.95rem; background: #f9fafb;" required>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #6d4c41; margin-bottom: 0.5rem;">Live Quails Being Cared For</label>
                            <input type="number" name="live_quails" id="liveQuailsInput" value="{{ $farm['live_quails'] ?? 0 }}" min="0" style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d7ccc8; border-radius: 10px; font-size: 0.95rem; background: #f9fafb;" placeholder="Enter number of live quails" required>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #6d4c41; margin-bottom: 0.5rem;">Dead Quails</label>
                            <input type="number" name="dead_quails" id="deadQuailsInput" value="{{ $farm['dead_quails'] ?? 0 }}" min="0" style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d7ccc8; border-radius: 10px; font-size: 0.95rem; background: #f9fafb;" placeholder="Enter number of dead quails">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #6d4c41; margin-bottom: 0.5rem;">Dressed Quails Ready for Sale</label>
                            <input type="number" name="dressed_quails_stock" id="dressedQuailsInput" value="{{ $farm['dressed_quails_stock'] ?? 0 }}" min="0" style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #d7ccc8; border-radius: 10px; font-size: 0.95rem; background: #f9fafb;" placeholder="Enter number of dressed quails">
                        </div>
                    </div>
                    <button type="button" onclick="openQuailUpdateModal()" style="background: linear-gradient(135deg, #6d4c41 0%, #a1887f 100%); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 10px; cursor: pointer; font-weight: 600; width: 100%; transition: all 0.3s ease; font-size: 0.95rem; box-shadow: 0 4px 12px rgba(161,136,127,0.2);" onmouseover="this.style.boxShadow='0 8px 20px rgba(161,136,127,0.3)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='0 4px 12px rgba(161,136,127,0.2)'; this.style.transform='translateY(0)';">Save Quail Inventory</button>
                </form>
            </div>

            <!-- Quail Statistics Table -->
            <div style="background: #efebe9; border-radius: 16px; padding: 1.5rem; margin-bottom: 2rem; border: 2px solid #d7ccc8;">
                <h3 style="color: #6d4c41; margin: 0 0 1rem 0;">Current Quail Statistics</h3>
                <table class="table" style="width: 100%; border-collapse: collapse; background: white;">
                    <thead>
                        <tr style="background: #efebe9;">
                            <th style="padding: 1rem; text-align: left; font-weight: 700; color: #6d4c41; border-bottom: 2px solid #d7ccc8;">Category</th>
                            <th style="padding: 1rem; text-align: right; font-weight: 700; color: #6d4c41; border-bottom: 2px solid #d7ccc8;">Count</th>
                            <th style="padding: 1rem; text-align: right; font-weight: 700; color: #6d4c41; border-bottom: 2px solid #d7ccc8;">Date</th>
                            <th style="padding: 1rem; text-align: right; font-weight: 700; color: #6d4c41; border-bottom: 2px solid #d7ccc8;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid #d7ccc8;">
                            <td style="padding: 1rem; color: #6d4c41; font-weight: 600;">Live Quails</td>
                            <td style="padding: 1rem; text-align: right; color: #4caf50; font-weight: 700; font-size: 1.1rem;" id="liveQuailsDisplay">{{ $farm['live_quails'] ?? 0 }}</td>
                            <td style="padding: 1rem; text-align: right; color: #8d6e63; font-size: 0.9rem;" id="quailDateDisplay">{{ isset($farm['quail_count_date']) ? date('M d, Y', strtotime($farm['quail_count_date'])) : 'N/A' }}</td>
                            <td style="padding: 1rem; text-align: right;"><span style="display: inline-block; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; background: #e8f5e9; color: #2e7d32;">Active</span></td>
                        </tr>
                        <tr style="border-bottom: 1px solid #d7ccc8;">
                            <td style="padding: 1rem; color: #6d4c41; font-weight: 600;">Dead Quails</td>
                            <td style="padding: 1rem; text-align: right; color: #d32f2f; font-weight: 700; font-size: 1.1rem;" id="deadQuailsDisplay">{{ $farm['dead_quails'] ?? 0 }}</td>
                            <td style="padding: 1rem; text-align: right; color: #8d6e63; font-size: 0.9rem;" id="deadQuailDateDisplay">{{ isset($farm['quail_count_date']) ? date('M d, Y', strtotime($farm['quail_count_date'])) : 'N/A' }}</td>
                            <td style="padding: 1rem; text-align: right;"><span style="display: inline-block; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; background: #ffebee; color: #c62828;">Recorded</span></td>
                        </tr>
                        <tr style="border-bottom: 1px solid #d7ccc8;">
                            <td style="padding: 1rem; color: #6d4c41; font-weight: 600;">Dressed Quails</td>
                            <td style="padding: 1rem; text-align: right; color: #ff9800; font-weight: 700; font-size: 1.1rem;" id="dressedQuailsDisplay">{{ $farm['dressed_quails_stock'] ?? 0 }}</td>
                            <td style="padding: 1rem; text-align: right; color: #8d6e63; font-size: 0.9rem;" id="dressedQuailsDateDisplay">{{ isset($farm['quail_count_date']) ? date('M d, Y', strtotime($farm['quail_count_date'])) : 'N/A' }}</td>
                            <td style="padding: 1rem; text-align: right;"><span style="display: inline-block; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; background: #fff3e0; color: #ef6c00;">Available</span></td>
                        </tr>
                        <tr style="background: #f5f0eb; font-weight: 700;">
                            <td style="padding: 1rem; color: #6d4c41;">Total Quails Raised</td>
                            <td style="padding: 1rem; text-align: right; color: #6d4c41; font-size: 1.1rem;" id="totalQuailsDisplay">{{ ($farm['live_quails'] ?? 0) + ($farm['dead_quails'] ?? 0) }}</td>
                            <td style="padding: 1rem; text-align: right; color: #8d6e63; font-size: 0.9rem;" id="totalQuailDateDisplay">{{ isset($farm['quail_count_date']) ? date('M d, Y', strtotime($farm['quail_count_date'])) : 'N/A' }}</td>
                            <td style="padding: 1rem; text-align: right;"><span style="display: inline-block; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; background: #ffebee; color: #c62828;">Recorded</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Mortality Rate -->
            <div style="background: linear-gradient(135deg, #fff9f5 0%, #fff7f2 100%); border-radius: 16px; padding: 1.5rem; border-left: 4px solid #ff9800;">
                <h4 style="color: #6d4c41; margin: 0 0 0.75rem 0;">Mortality Statistics</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div>
                        <p style="margin: 0; color: #8d6e63; font-size: 0.9rem;">Mortality Rate</p>
                        <p style="margin: 0.5rem 0 0; color: #6d4c41; font-weight: 700; font-size: 1.5rem;" id="mortalityRateDisplay">
                            @php
                                $liveQuails = $farm['live_quails'] ?? 0;
                                $deadQuails = $farm['dead_quails'] ?? 0;
                                $totalQuails = $liveQuails + $deadQuails;
                                $mortalityRate = $totalQuails > 0 ? round(($deadQuails / $totalQuails) * 100, 2) : 0;
                            @endphp
                            {{ $mortalityRate }}%
                        </p>
                    </div>
                    <div>
                        <p style="margin: 0; color: #8d6e63; font-size: 0.9rem;">Survival Rate</p>
                        <p style="margin: 0.5rem 0 0; color: #6d4c41; font-weight: 700; font-size: 1.5rem;" id="survivalRateDisplay">
                            {{ 100 - $mortalityRate }}%
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ORDERS & PRODUCTS Category removed -->

    <!-- Price Edit Modal -->
    <div class="confirm-modal-overlay" id="priceEditModal">
        <div class="confirm-modal">
            <div class="confirm-modal-title">Edit Product Price</div>
            <input type="number" class="edit-modal-input" id="priceEditInput" step="0.01" min="0" placeholder="Enter new price" style="margin: 1.5rem 0; padding: 0.75rem; border: 2px solid #d4a574; border-radius: 8px; font-size: 1rem; width: 100%; box-sizing: border-box;">
            <div class="confirm-modal-buttons">
                <button class="confirm-modal-btn cancel" onclick="closeEditModal('priceEditModal')">Cancel</button>
                <button class="confirm-modal-btn confirm" onclick="savePriceEdit()">Save Price</button>
            </div>
        </div>
    </div>

    <!-- Stock Edit Modal -->
    <div class="confirm-modal-overlay" id="stockEditModal">
        <div class="confirm-modal">
            <div class="confirm-modal-title">Edit Stock Level</div>
            <input type="number" class="edit-modal-input" id="stockEditInput" min="0" placeholder="Enter stock quantity" style="margin: 1.5rem 0; padding: 0.75rem; border: 2px solid #d4a574; border-radius: 8px; font-size: 1rem; width: 100%; box-sizing: border-box;">
            <div class="confirm-modal-buttons">
                <button class="confirm-modal-btn cancel" onclick="closeEditModal('stockEditModal')">Cancel</button>
                <button class="confirm-modal-btn confirm" onclick="saveStockEdit()">Save Stock</button>
            </div>
        </div>
    </div>

    <!-- ANALYTICS & REPORTS Category -->
    <div id="analytics" class="category-content">
        <div class="section-card">
            <h2 class="section-title">Analytics & Reports</h2>
            <p style="color: #8d6e63; margin-bottom: 1.5rem;">Analyze your farm's performance with detailed insights and reports.</p>
            
            <!-- Date Range Filter -->
            <div style="background: linear-gradient(135deg, #f5f0eb 0%, #efe8e4 100%); border-radius: 16px; padding: 1.5rem; margin-bottom: 3rem; border: 2px solid #d7ccc8; box-shadow: 0 4px 12px rgba(161, 136, 127, 0.1);">
                <h3 style="color: #6d4c41; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">Date Range Filter</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">From Date</label>
                        <input type="date" class="form-input" id="analyticsFromDate" value="{{ date('Y-m-d', strtotime('-30 days')) }}" onchange="updateRealAnalyticsData()">
                    </div>
                    <div class="form-group">
                        <label class="form-label">To Date</label>
                        <input type="date" class="form-input" id="analyticsToDate" value="{{ date('Y-m-d') }}" onchange="updateRealAnalyticsData()">
                    </div>
                </div>
                
                <!-- Export Options -->
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid #d7ccc8;">
                    <h4 style="color: #6d4c41; margin: 0 0 1rem 0;">Export Reports</h4>
                    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                        <button onclick="openExportModal('print')" class="btn btn-primary" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem 1.5rem; min-width: 140px;">
                            Print Report
                        </button>
                        <button onclick="openExportModal('pdf')" class="btn" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem 1.5rem; min-width: 140px; background: #d32f2f; color: white; border: none;">
                            Export PDF
                        </button>
                    </div>
                    <div style="margin-top: 1rem; text-align: center;">
                        <small style="color: #8d6e63; font-size: 0.8rem;">Print or export analytics report for record keeping</small>
                    </div>
                </div>
            </div>
            

            
            <!-- PRODUCTION SECTION -->
            <div style="background: linear-gradient(135deg, rgba(121, 85, 72, 0.05) 0%, rgba(141, 110, 99, 0.05) 100%); border-left: 6px solid #795548; padding: 0.5rem 0 1.5rem 0; margin-bottom: 3rem;">
                <h2 style="color: #6d4c41; font-size: 1.5rem; margin: 0 0 1.5rem 0; padding: 0 2rem;">Production Analytics</h2>
                
                <!-- Egg Collection Analytics -->
                <div class="section-card" style="margin-left: 2rem; margin-right: 2rem;">
                    <h3 style="color: #6d4c41; margin: 0 0 1rem 0;">Egg Collection Metrics</h3>
                <div class="info-grid">
                    <div class="info-card" style="border-left: 4px solid #795548;">
                        <div class="info-card-title">Total Eggs Collected</div>
                        <div class="info-card-text" id="analyticsEggsCollected">Loading...</div>
                    </div>
                    <div class="info-card" style="border-left: 4px solid #ff9800;">
                        <div class="info-card-title">Available Eggs After Sales</div>
                        <div class="info-card-text" id="analyticsAvailableEggs">Loading...</div>
                    </div>
                    <div class="info-card" style="border-left: 4px solid #4caf50;">
                        <div class="info-card-title">Good Eggs</div>
                        <div class="info-card-text" id="analyticsGoodEggs">Loading...</div>
                    </div>
                    <div class="info-card" style="border-left: 4px solid #f44336;">
                        <div class="info-card-title">Cracked Eggs</div>
                        <div class="info-card-text" id="analyticsCrackedEggs">Loading...</div>
                    </div>
                    <div class="info-card" style="border-left: 4px solid #2196f3;">
                        <div class="info-card-title">Quality Rate</div>
                        <div class="info-card-text" id="analyticsQualityRate">Loading...</div>
                    </div>
                </div>
                
                <!-- Collection Chart -->
                <div style="margin-top: 2rem; background: #f9f9f9; border-radius: 12px; padding: 1.5rem;">
                    <h4 style="color: #6d4c41; margin-bottom: 1rem;">Daily Egg Collection Trend</h4>
                    <div style="height: 300px; position: relative;">
                        <canvas id="dailyCollectionChart"></canvas>
                    </div>
                </div>
                </div>
            </div>
            
            <!-- SALES SECTION -->
            <div style="background: linear-gradient(135deg, rgba(255, 152, 0, 0.05) 0%, rgba(255, 193, 7, 0.05) 100%); border-left: 6px solid #ff9800; padding: 0.5rem 0 1.5rem 0; margin-bottom: 3rem;">
                <h2 style="color: #6d4c41; font-size: 1.5rem; margin: 0 0 1.5rem 0; padding: 0 2rem;">Sales Analytics</h2>
                
                <!-- Sales Data Section -->
                <div class="section-card" style="margin-left: 2rem; margin-right: 2rem;">
                    <h3 style="color: #6d4c41; margin: 0 0 1rem 0;">Sales Performance</h3>
                    <div id="salesPerformanceSummary" style="margin-bottom: 1rem; padding: 0.9rem 1rem; background: #fff8f2; border: 1px solid #f0d7c8; border-radius: 12px; color: #6d4c41; font-weight: 600;">
                        Based on Record Sales entries.
                    </div>
                <div class="info-grid">
                    <div class="info-card" style="border-left: 4px solid #795548;">
                        <div class="info-card-title">Eggs Sold</div>
                        <div class="info-card-text" id="analyticsEggsSold">Loading...</div>
                    </div>
                    <div class="info-card" style="border-left: 4px solid #4caf50;">
                        <div class="info-card-title">Live Quail Sold</div>
                        <div class="info-card-text" id="analyticsLiveQuailSold">Loading...</div>
                    </div>
                    <div class="info-card" style="border-left: 4px solid #ff9800;">
                        <div class="info-card-title">Dressed Quail Sold</div>
                        <div class="info-card-text" id="analyticsDressedQuailSold">Loading...</div>
                    </div>
                </div>
                
                <!-- Sales Chart -->
                <div style="margin-top: 2rem; background: #f9f9f9; border-radius: 12px; padding: 1.5rem;">
                    <h4 style="color: #6d4c41; margin-bottom: 1rem;">Product Sales Distribution</h4>
                    <div style="height: 300px; position: relative;">
                        <canvas id="productSalesChart"></canvas>
                    </div>
                    
                    <!-- Product Sales Table -->
                    <div style="margin-top: 2rem; overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden;">
                            <thead>
                                <tr style="background: #f5f5f5;">
                                    <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.9rem;">Product</th>
                                    <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.9rem;">Quantity Sold</th>
                                    <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.9rem;">Revenue</th>
                                    <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.9rem;">Percentage</th>
                                </tr>
                            </thead>
                            <tbody id="productSalesTableBody">
                                <tr>
                                    <td colspan="4" style="padding: 2rem; text-align: center; color: #a1887f;">Loading sales data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
            </div>
            
            <!-- FARM STATISTICS SECTION -->
            <div style="background: linear-gradient(135deg, rgba(76, 175, 80, 0.05) 0%, rgba(46, 125, 50, 0.05) 100%); border-left: 6px solid #4caf50; padding: 0.5rem 0 1.5rem 0; margin-bottom: 2rem;">
                <h2 style="color: #6d4c41; font-size: 1.5rem; margin: 0 0 1.5rem 0; padding: 0 2rem;">Farm Statistics & Health</h2>
                
                <!-- Quail Farm Statistics -->
                <div class="section-card" style="margin-left: 2rem; margin-right: 2rem;">
                    <h3 style="color: #6d4c41; margin: 0 0 1rem 0;">Quail Population Overview</h3>
                <div class="info-grid">
                    <div class="info-card" style="border-left: 4px solid #4caf50;">
                        <div class="info-card-title">Live Quails</div>
                        <div class="info-card-text" id="farmStatsLiveQuails">{{ $farm['live_quails'] ?? 0 }}</div>
                    </div>
                    <div class="info-card" style="border-left: 4px solid #d32f2f;">
                        <div class="info-card-title">Dead Quails</div>
                        <div class="info-card-text" id="farmStatsDeadQuails">{{ $farm['dead_quails'] ?? 0 }}</div>
                    </div>
                    <div class="info-card" style="border-left: 4px solid #ff9800;">
                        <div class="info-card-title">Dressed Quails</div>
                        <div class="info-card-text" id="farmStatsDressedQuails">{{ $farm['dressed_quails_stock'] ?? 0 }}</div>
                    </div>
                    <div class="info-card" style="border-left: 4px solid #2196f3;">
                        <div class="info-card-title">Total Raised</div>
                        <div class="info-card-text" id="farmStatsTotalQuails">{{ ($farm['live_quails'] ?? 0) + ($farm['dead_quails'] ?? 0) }}</div>
                    </div>
                    <div class="info-card" style="border-left: 4px solid #9c27b0;">
                        <div class="info-card-title">Mortality Rate</div>
                        <div class="info-card-text" id="farmStatsMortalityRate">
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
                
                <!-- Statistics Table -->
                <div style="margin-top: 2rem;">
                    <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <thead>
                            <tr style="background: linear-gradient(135deg, #6d4c41, #8d6e63); color: white;">
                                <th style="padding: 1rem; text-align: left; font-weight: 700;">Metric</th>
                                <th style="padding: 1rem; text-align: center; font-weight: 700;">Count</th>
                                <th style="padding: 1rem; text-align: center; font-weight: 700;">Percentage</th>
                                <th style="padding: 1rem; text-align: left; font-weight: 700;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom: 1px solid #efebe9; background: #fafafa;">
                                <td style="padding: 1rem; color: #6d4c41; font-weight: 600;">Live Quails</td>
                                <td style="padding: 1rem; text-align: center; color: #8d6e63; font-weight: 700;">{{ $liveQuails }}</td>
                                <td style="padding: 1rem; text-align: center; color: #4caf50; font-weight: 700;">
                                    @php
                                        $livePercentage = $totalQuails > 0 ? round(($liveQuails / $totalQuails) * 100, 2) : 0;
                                    @endphp
                                    {{ $livePercentage }}%
                                </td>
                                <td style="padding: 1rem; color: #4caf50;">
                                    <span style="background: #c8e6c9; color: #2e7d32; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">Active</span>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #efebe9; background: white;">
                                <td style="padding: 1rem; color: #6d4c41; font-weight: 600;">Dead Quails</td>
                                <td style="padding: 1rem; text-align: center; color: #8d6e63; font-weight: 700;">{{ $deadQuails }}</td>
                                <td style="padding: 1rem; text-align: center; color: #d32f2f; font-weight: 700;">{{ $mortalityRate }}%</td>
                                <td style="padding: 1rem; color: #d32f2f;">
                                    <span style="background: #ffcdd2; color: #c62828; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">Inactive</span>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #efebe9; background: #fafafa;">
                                <td style="padding: 1rem; color: #6d4c41; font-weight: 600;">Total Raised</td>
                                <td style="padding: 1rem; text-align: center; color: #8d6e63; font-weight: 700;">{{ $totalQuails }}</td>
                                <td style="padding: 1rem; text-align: center; color: #6d4c41; font-weight: 700;">100%</td>
                                <td style="padding: 1rem; color: #2196f3;">
                                    <span style="background: #bbdefb; color: #1565c0; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">Total</span>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #efebe9; background: white;">
                                 <td style="padding: 1rem; color: #6d4c41; font-weight: 600;">Dressed Quails</td>
                                 <td style="padding: 1rem; text-align: center; color: #8d6e63; font-weight: 700;">{{ $farm['dressed_quails_stock'] ?? 0 }}</td>
                                 <td style="padding: 1rem; text-align: center; color: #ff9800; font-weight: 700;">
                                     @php
                                         $dressedQuails = $farm['dressed_quails_stock'] ?? 0;
                                         $dressedPercentage = $totalQuails > 0 ? round(($dressedQuails / $totalQuails) * 100, 2) : 0;
                                     @endphp
                                     {{ $dressedPercentage }}%
                                 </td>
                                 <td style="padding: 1rem; color: #ff9800;">
                                     <span style="background: #fff3e0; color: #ef6c00; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">Available</span>
                                 </td>
                             </tr>
                             <tr style="background: #f5f0eb;">
                                <td style="padding: 1rem; color: #6d4c41; font-weight: 600;">Survival Rate</td>
                                <td style="padding: 1rem; text-align: center; color: #8d6e63; font-weight: 700;">{{ $liveQuails }}</td>
                                <td style="padding: 1rem; text-align: center; color: #4caf50; font-weight: 700;">{{ round(100 - $mortalityRate, 2) }}%</td>
                                <td style="padding: 1rem; color: #4caf50;">
                                    <span style="background: #e8f5e9; color: #1b5e20; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">Healthy</span>
                                </td>
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
window.quailInventoryState = {
    live_quails: {{ (int)($farm['live_quails'] ?? 0) }},
    dressed_quails_stock: {{ (int)($farm['dressed_quails_stock'] ?? 0) }}
};

// Load current breed information from API
function loadCurrentBreedInfo() {
    fetch('/api/breeds/current')
        .then(response => response.json())
        .then(breed => {
            if (breed) {
                console.log('Current breed:', breed.name);
                console.log('Egg production rate:', breed.egg_production_rate, 'eggs/year');
                console.log('Mature weight:', breed.mature_weight, 'grams');
                console.log('Maturity age:', breed.maturity_age, 'days');
                
                // You can use this breed information anywhere in your JavaScript
                // For example, to calculate expected production:
                const dailyEggProduction = Math.round(breed.egg_production_rate / 365);
                console.log('Expected daily production:', dailyEggProduction, 'eggs/day');
            }
        })
        .catch(error => {
            console.error('Error loading breed info:', error);
        });
}

// Call this function when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadCurrentBreedInfo();
});

// Global Chart Functions
let bestSellingChartInstance = null;
let dailyProductionChartInstance = null;

// Update Best Selling Products Chart
function updateBestSellingChart(eggsSold, liveQuailSold, dressedQuailSold) {
    // Check if we have a table instead of chart
    const tableBody = document.getElementById('bestSellingTableBody');
    if (tableBody) {
        updateBestSellingTable(eggsSold, liveQuailSold, dressedQuailSold);
        return;
    }
    
    const ctx = document.getElementById('bestSellingChart');
    if (!ctx) {
        console.log('Best selling chart canvas not found');
        return;
    }
    
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.log('Chart.js not loaded yet, skipping chart update');
        return;
    }
    
    // Destroy existing chart
    if (bestSellingChartInstance) {
        bestSellingChartInstance.destroy();
    }
    
    bestSellingChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Quail Eggs', 'Live Quail', 'Dressed Quail'],
            datasets: [{
                data: [eggsSold, liveQuailSold, dressedQuailSold],
                backgroundColor: ['#a1887f', '#8d6e63', '#6d4c41'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const unit = label.includes('Eggs') ? ' trays' : ' pcs';
                            return label + ': ' + value + unit;
                        }
                    }
                }
            }
        }
    });
    console.log('Best selling chart updated');
}

// Update Best Selling Products Table
function updateBestSellingTable(eggsSold, liveQuailSold, dressedQuailSold) {
    const tableBody = document.getElementById('bestSellingTableBody');
    if (!tableBody) return;
    
    // Use provided data or generate consistent sample data
    const salesData = [
        {
            product: 'Quail Eggs',
            quantity: eggsSold || Math.floor(Math.random() * 50) + 20,
            unit: 'trays',
            price: 160.00,
            percentage: 0
        },
        {
            product: 'Live Quail', 
            quantity: liveQuailSold || Math.floor(Math.random() * 20) + 5,
            unit: 'pcs',
            price: 180.00,
            percentage: 0
        },
        {
            product: 'Dressed Quail',
            quantity: dressedQuailSold || Math.floor(Math.random() * 15) + 3,
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
        <td style="padding: 1rem; font-size: 0.9rem; color: #000; font-weight: 700;">₱${item.total.toLocaleString()}.00</td>
        <td style="padding: 1rem; font-size: 0.9rem; color: #000;">${item.percentage}%</td>
    </tr>
`).join('');
    
    console.log('Best selling table updated with data:', salesData);
}

// Update Daily Production Chart
function updateDailyProductionChart(fromDate, toDate) {
    const ctx = document.getElementById('dailyProductionChart');
    if (!ctx) {
        console.log('Daily production chart canvas not found');
        return;
    }
    
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.log('Chart.js not loaded yet, skipping chart update');
        return;
    }
    
    // Destroy existing chart
    if (dailyProductionChartInstance) {
        dailyProductionChartInstance.destroy();
    }
    
    // Get daily production data
    const dailyData = getDailyProductionData(fromDate, toDate);
    
    dailyProductionChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dailyData.labels,
            datasets: [{
                label: 'Eggs Produced',
                data: dailyData.values,
                borderColor: '#a1887f',
                backgroundColor: 'rgba(161, 136, 127, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#6d4c41',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#efebe9'
                    },
                    ticks: {
                        color: '#6d4c41'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6d4c41',
                        maxTicksLimit: 7
                    }
                }
            }
        }
    });
    console.log('Daily production chart updated');
}

// Get daily production data for chart
function getDailyProductionData(fromDate, toDate) {
    const dailyTotals = {};
    
    // Process collection records
    if (typeof collectionRecords !== 'undefined') {
        collectionRecords.forEach(record => {
            const recordDate = new Date(record.timestamp);
            if (recordDate >= fromDate && recordDate <= toDate) {
                const dateStr = recordDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                dailyTotals[dateStr] = (dailyTotals[dateStr] || 0) + (record.eggs || 0);
            }
        });
    }
    
    // Process production records
    if (typeof productionRecords !== 'undefined') {
        productionRecords.forEach(record => {
            const recordDate = new Date(record.timestamp);
            if (recordDate >= fromDate && recordDate <= toDate) {
                const dateStr = recordDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                dailyTotals[dateStr] = (dailyTotals[dateStr] || 0) + (record.totalEggs || 0);
            }
        });
    }
    
    // If no data, show sample data
    if (Object.keys(dailyTotals).length === 0) {
        return {
            labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
            values: [0, 0, 0, 0, 0, 0, 0]
        };
    }
    
    // Sort by date and return
    const sortedEntries = Object.entries(dailyTotals).sort((a, b) => {
        return new Date(a[0] + ', 2024') - new Date(b[0] + ', 2024');
    });
    
    return {
        labels: sortedEntries.map(entry => entry[0]),
        values: sortedEntries.map(entry => entry[1])
    };
}

// Quick Date Range Functions
function setQuickDateRange(range) {
    const fromDateInput = document.getElementById('analyticsFromDate');
    const toDateInput = document.getElementById('analyticsToDate');
    
    if (!fromDateInput || !toDateInput) return;
    
    const today = new Date();
    let fromDate, toDate;
    
    switch(range) {
        case 'today':
            fromDate = today;
            toDate = today;
            break;
        case 'week':
            fromDate = new Date(today);
            fromDate.setDate(today.getDate() - 7);
            toDate = today;
            break;
        case 'month':
            fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
            toDate = today;
            break;
        default:
            return;
    }
    
    fromDateInput.value = fromDate.toISOString().split('T')[0];
    toDateInput.value = toDate.toISOString().split('T')[0];
    
    // Auto-update analytics
    updateRealAnalyticsData();
}

// Global Export Functions

// Export Modal Functions
function openExportModal(type) {
    const modal = document.getElementById('exportReportModal');
    const modalTitle = document.getElementById('exportModalTitle');
    const modalContent = document.getElementById('exportModalContent');
    
    if (type === 'print') {
        modalTitle.textContent = 'Print Report';
        modalContent.innerHTML = `
            <p style="color: #6d4c41; margin-bottom: 1rem; font-size: 0.95rem;">Are you sure you want to print the analytics report?</p>
            <div style="background: #f5f0eb; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #a1887f;">
                <p style="margin: 0; color: #8d6e63; font-size: 0.85rem;">
                    <strong>Report includes:</strong> Total Orders, Total Revenue, Egg Collection & Quality data for the selected date range
                </p>
            </div>
        `;
    } else if (type === 'pdf') {
        modalTitle.textContent = 'Export as PDF';
        modalContent.innerHTML = `
            <p style="color: #6d4c41; margin-bottom: 1rem; font-size: 0.95rem;">Are you sure you want to export the analytics report as PDF?</p>
            <div style="background: #f5f0eb; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #a1887f;">
                <p style="margin: 0; color: #8d6e63; font-size: 0.85rem;">
                    <strong>Report includes:</strong> Total Orders, Total Revenue, Egg Collection & Quality data for the selected date range
                </p>
            </div>
        `;
    }
    
    // Update action buttons
    const confirmBtn = document.getElementById('exportConfirmBtn');
    confirmBtn.onclick = function() {
        closeExportModal();
        if (type === 'print') {
            printAnalytics();
        } else if (type === 'pdf') {
            exportToPDF();
        }
    };
    
    // Show modal
    modal.classList.add('show');
}

function closeExportModal() {
    const modal = document.getElementById('exportReportModal');
    modal.classList.remove('show');
}

// Close modal when clicking outside of it
document.addEventListener('click', function(event) {
    const modal = document.getElementById('exportReportModal');
    if (modal && event.target == modal) {
        modal.classList.remove('show');
    }
});

function printAnalytics() {
    console.log('Printing professional analytics report...');
    const printContent = generateProfessionalReport();
    const printWindow = window.open('', '', 'height=800,width=900');
    printWindow.document.write(printContent);
    printWindow.document.close();
    setTimeout(() => {
        printWindow.print();
    }, 250);
}

function exportToPDF() {
    console.log('Exporting to PDF...');
    
    // Check if jsPDF is available
    if (typeof window.jsPDF === 'undefined') {
        // Load jsPDF dynamically
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
        script.onload = function() {
            generatePDFReport();
        };
        script.onerror = function() {
            alert('Failed to load PDF library. Please check your internet connection and try again.');
        };
        document.head.appendChild(script);
    } else {
        generatePDFReport();
    }
}

function generatePDFReport() {
    try {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        // Get data
        const fromDate = document.getElementById('analyticsFromDate')?.value || 'N/A';
        const toDate = document.getElementById('analyticsToDate')?.value || 'N/A';
        const currentDate = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        
        const eggCollected = document.getElementById('analyticsEggsCollected')?.textContent || '0 eggs';
        const goodEggs = document.getElementById('analyticsGoodEggs')?.textContent || '0 eggs';
        const crackedEggs = document.getElementById('analyticsCrackedEggs')?.textContent || '0 eggs';
        const qualityRate = document.getElementById('analyticsQualityRate')?.textContent || '0%';
        const eggsSold = document.getElementById('analyticsEggsSold')?.textContent || '0 trays';
        const liveQuailSold = document.getElementById('analyticsLiveQuailSold')?.textContent || '0 pcs';
        const dressedQuailSold = document.getElementById('analyticsDressedQuailSold')?.textContent || '0 pcs';
        
        // Extract numbers for revenue calculation
        const extractNumber = (str) => parseInt(str.match(/\d+/)?.[0] || '0');
        const eggsSoldQty = extractNumber(eggsSold);
        const liveQuailQty = extractNumber(liveQuailSold);
        const dressedQuailQty = extractNumber(dressedQuailSold);
        
        const eggsRevenue = eggsSoldQty * 160;
        const liveQuailRevenue = liveQuailQty * 180;
        const dressedQuailRevenue = dressedQuailQty * 250;
        const totalRevenue = eggsRevenue + liveQuailRevenue + dressedQuailRevenue;
        
        let yPos = 20;
        
        // Header
        doc.setFontSize(20);
        doc.setTextColor(109, 76, 65);
        doc.text('ESCALONA\'S FARM', 105, yPos, { align: 'center' });
        yPos += 7;
        
        doc.setFontSize(16);
        doc.setTextColor(141, 110, 99);
        doc.text('Quail Farm Analytics Report', 105, yPos, { align: 'center' });
        yPos += 6;
        
        doc.setFontSize(11);
        doc.setTextColor(153, 153, 153);
        doc.text('Intelligent Quail Farm Management System (SQUIFM)', 105, yPos, { align: 'center' });
        yPos += 8;
        
        // Separator line
        doc.setDrawColor(109, 76, 65);
        doc.line(15, yPos, 195, yPos);
        yPos += 5;
        
        // Metadata
        doc.setFontSize(11);
        doc.setTextColor(100, 100, 100);
        doc.text('Report Period: ' + fromDate + ' to ' + toDate, 20, yPos);
        yPos += 5;
        doc.text('Generated: ' + currentDate, 20, yPos);
        yPos += 5;
        doc.text('Location: Pagkakaisa, Naujan, Oriental Mindoro', 20, yPos);
        yPos += 8;
        
        // PRODUCTION SECTION
        doc.setFontSize(14);
        doc.setTextColor(109, 76, 65);
        doc.text('PRODUCTION ANALYTICS', 20, yPos);
        yPos += 7;
        
        doc.setFontSize(11);
        doc.setTextColor(0, 0, 0);
        doc.text('Total Eggs Collected: ' + eggCollected, 20, yPos);
        yPos += 5;
        doc.text('Good Eggs: ' + goodEggs, 20, yPos);
        yPos += 5;
        doc.text('Cracked Eggs: ' + crackedEggs, 20, yPos);
        yPos += 5;
        doc.text('Quality Rate: ' + qualityRate, 20, yPos);
        yPos += 8;
        
        // SALES SECTION
        doc.setFontSize(14);
        doc.setTextColor(109, 76, 65);
        doc.text('SALES ANALYTICS', 20, yPos);
        yPos += 7;
        
        // Sales table headers
        doc.setFontSize(10);
        doc.setTextColor(255, 255, 255);
        doc.setFillColor(141, 110, 99);
        doc.rect(20, yPos - 3, 175, 5, 'F');
        
        doc.text('Product', 25, yPos);
        doc.text('Qty', 90, yPos, { align: 'center' });
        doc.text('Unit Price', 115, yPos, { align: 'right' });
        doc.text('Total Revenue', 165, yPos, { align: 'right' });
        yPos += 6;
        
        // Sales table data
        doc.setTextColor(0, 0, 0);
        doc.text('Quail Eggs', 25, yPos);
        doc.text(eggsSoldQty + ' trays', 90, yPos, { align: 'center' });
        doc.text('P 160.00', 115, yPos, { align: 'right' });
        doc.text('P ' + eggsRevenue.toLocaleString() + '.00', 165, yPos, { align: 'right' });
        yPos += 5;
        
        doc.text('Live Quail', 25, yPos);
        doc.text(liveQuailQty + ' pcs', 90, yPos, { align: 'center' });
        doc.text('P 180.00', 115, yPos, { align: 'right' });
        doc.text('P ' + liveQuailRevenue.toLocaleString() + '.00', 165, yPos, { align: 'right' });
        yPos += 5;
        
        doc.text('Dressed Quail', 25, yPos);
        doc.text(dressedQuailQty + ' pcs', 90, yPos, { align: 'center' });
        doc.text('P 250.00', 115, yPos, { align: 'right' });
        doc.text('P ' + dressedQuailRevenue.toLocaleString() + '.00', 165, yPos, { align: 'right' });
        yPos += 7;
        
        // Total row
        doc.setFillColor(245, 240, 235);
        doc.rect(20, yPos - 3, 175, 5, 'F');
        doc.setTextColor(109, 76, 65);
        doc.setFont(undefined, 'bold');
        doc.text('TOTAL REVENUE', 25, yPos);
        doc.text('P ' + totalRevenue.toLocaleString() + '.00', 165, yPos, { align: 'right' });
        doc.setFont(undefined, 'normal');
        yPos += 8;
        
        // Footer
        doc.setFontSize(10);
        doc.setTextColor(150, 150, 150);
        doc.line(20, 275, 190, 275);
        doc.text('This report is confidential and generated by SQUIFM System', 105, 280, { align: 'center' });
        doc.text('Â© 2026 Escalona\'s Farm - All Rights Reserved', 105, 285, { align: 'center' });
        
        // Save the PDF
        const fileName = `SQUIFM_Analytics_Report_${currentDate.replace(/ /g, '_')}.pdf`;
        doc.save(fileName);
        
        // Show success notification
        if (typeof addNotification === 'function') {
            addNotification('', `PDF report exported successfully: ${fileName}`, 'success');
        } else {
            alert(`PDF report exported successfully: ${fileName}`);
        }
        
    } catch (error) {
        console.error('Error generating PDF:', error);
        alert('Error generating PDF report. Please try again.');
    }
}

function generateProfessionalReport() {
    const fromDate = document.getElementById('analyticsFromDate')?.value || 'N/A';
    const toDate = document.getElementById('analyticsToDate')?.value || 'N/A';
    const currentDate = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
    
    const eggCollected = document.getElementById('analyticsEggsCollected')?.textContent || '0 eggs';
    const goodEggs = document.getElementById('analyticsGoodEggs')?.textContent || '0 eggs';
    const crackedEggs = document.getElementById('analyticsCrackedEggs')?.textContent || '0 eggs';
    const qualityRate = document.getElementById('analyticsQualityRate')?.textContent || '0%';
    const eggsSold = document.getElementById('analyticsEggsSold')?.textContent || '0 trays';
    const liveQuailSold = document.getElementById('analyticsLiveQuailSold')?.textContent || '0 pcs';
    const dressedQuailSold = document.getElementById('analyticsDressedQuailSold')?.textContent || '0 pcs';
    
    // Calculate revenue
    const extractNumber = (str) => parseInt(str.match(/\d+/)?.[0] || '0');
    const eggsSoldQty = extractNumber(eggsSold);
    const liveQuailQty = extractNumber(liveQuailSold);
    const dressedQuailQty = extractNumber(dressedQuailSold);
    
    const eggsRevenue = eggsSoldQty * 160;
    const liveQuailRevenue = liveQuailQty * 180;
    const dressedQuailRevenue = dressedQuailQty * 250;
    const totalRevenue = eggsRevenue + liveQuailRevenue + dressedQuailRevenue;
    
    return `
    <!DOCTYPE html>
    <html>
    <head>
        <title>SQUIFM Analytics Report</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; line-height: 1.6; background: #fff; }
            .page { width: 8.5in; height: 11in; margin: 0 auto; padding: 0.5in; background: white; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
            @media print { .page { box-shadow: none; margin: 0; padding: 0.5in; } body { background: white; } }
            
            /* Header */
            .header { text-align: center; margin-bottom: 1.5in; border-bottom: 3px solid #6d4c41; padding-bottom: 0.3in; }
            .company-name { font-size: 28px; font-weight: bold; color: #6d4c41; margin-bottom: 0.1in; }
            .report-title { font-size: 20px; color: #8d6e63; margin-bottom: 0.2in; }
            .report-meta { font-size: 11px; color: #999; line-height: 1.4; }
            
            /* Metadata */
            .metadata { margin-bottom: 0.3in; padding: 0.15in 0.2in; background: #f5f0eb; border-left: 4px solid #a1887f; font-size: 11px; }
            .metadata-row { display: flex; justify-content: space-between; margin-bottom: 0.05in; }
            .metadata-label { font-weight: bold; color: #6d4c41; width: 1.5in; }
            .metadata-value { color: #4e342e; }
            
            /* Section */
            .section { margin-bottom: 0.4in; page-break-inside: avoid; }
            .section-title { font-size: 14px; font-weight: bold; color: white; background: #6d4c41; padding: 0.15in 0.2in; margin-bottom: 0.15in; border-radius: 0.1in; }
            
            /* Metrics Grid */
            .metrics-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.2in; margin-bottom: 0.2in; }
            .metric-box { border: 1px solid #d7ccc8; padding: 0.15in; text-align: center; border-radius: 0.05in; background: #fafafa; }
            .metric-label { font-size: 10px; color: #8d6e63; margin-bottom: 0.05in; }
            .metric-value { font-size: 16px; font-weight: bold; color: #6d4c41; }
            
            /* Table */
            .data-table { width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 0.2in; }
            .data-table th { background: #8d6e63; color: white; padding: 0.1in; text-align: left; font-weight: bold; }
            .data-table td { padding: 0.08in 0.1in; border-bottom: 1px solid #d7ccc8; }
            .data-table tr:nth-child(even) { background: #fafafa; }
            
            /* Summary Box */
            .summary-box { background: #efebe9; border-left: 4px solid #6d4c41; padding: 0.15in 0.2in; margin-bottom: 0.2in; }
            .summary-row { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 0.05in; font-weight: bold; }
            .summary-label { color: #6d4c41; }
            .summary-value { color: #4e342e; }
            
            /* Footer */
            .footer { margin-top: 0.3in; padding-top: 0.2in; border-top: 2px solid #d7ccc8; font-size: 10px; color: #999; text-align: center; }
            .footer-text { margin-bottom: 0.05in; }
        </style>
    </head>
    <body>
        <div class="page">
            <!-- HEADER -->
            <div class="header">
                <div class="company-name">ESCALONA'S FARM</div>
                <div class="report-title">Quail Farm Analytics Report</div>
                <div class="report-meta">Intelligent Quail Farm Management System (SQUIFM)</div>
            </div>
            
            <!-- METADATA -->
            <div class="metadata">
                <div class="metadata-row">
                    <span class="metadata-label">Report Period:</span>
                    <span class="metadata-value">${fromDate} to ${toDate}</span>
                </div>
                <div class="metadata-row">
                    <span class="metadata-label">Generated:</span>
                    <span class="metadata-value">${currentDate}</span>
                </div>
                <div class="metadata-row">
                    <span class="metadata-label">Location:</span>
                    <span class="metadata-value">Pagkakaisa, Naujan, Oriental Mindoro</span>
                </div>
            </div>
            
            <!-- PRODUCTION SECTION -->
            <div class="section">
                <div class="section-title">PRODUCTION ANALYTICS</div>
                <div class="metrics-grid">
                    <div class="metric-box"><div class="metric-label">Total Collected</div><div class="metric-value">${eggCollected}</div></div>
                    <div class="metric-box"><div class="metric-label">Good Eggs</div><div class="metric-value">${goodEggs}</div></div>
                    <div class="metric-box"><div class="metric-label">Cracked Eggs</div><div class="metric-value">${crackedEggs}</div></div>
                    <div class="metric-box"><div class="metric-label">Quality Rate</div><div class="metric-value">${qualityRate}</div></div>
                </div>
            </div>
            
            <!-- SALES SECTION -->
            <div class="section">
                <div class="section-title">SALES ANALYTICS</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th style="text-align: center;">Qty</th>
                            <th style="text-align: right;">Unit Price</th>
                            <th style="text-align: right;">Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Quail Eggs</td>
                            <td style="text-align: center;">${eggsSoldQty} trays</td>
                            <td style="text-align: right;">P 160.00</td>
                            <td style="text-align: right; font-weight: bold;">P ${eggsRevenue.toLocaleString()}.00</td>
                        </tr>
                        <tr>
                            <td>Live Quail</td>
                            <td style="text-align: center;">${liveQuailQty} pcs</td>
                            <td style="text-align: right;">P 180.00</td>
                            <td style="text-align: right; font-weight: bold;">P ${liveQuailRevenue.toLocaleString()}.00</td>
                        </tr>
                        <tr>
                            <td>Dressed Quail</td>
                            <td style="text-align: center;">${dressedQuailQty} pcs</td>
                            <td style="text-align: right;">P 250.00</td>
                            <td style="text-align: right; font-weight: bold;">P ${dressedQuailRevenue.toLocaleString()}.00</td>
                        </tr>
                    </tbody>
                </table>
                
                <!-- Summary -->
                <div class="summary-box">
                    <div class="summary-row">
                        <span class="summary-label">TOTAL REVENUE:</span>
                        <span class="summary-value">P ${totalRevenue.toLocaleString()}.00</span>
                    </div>
                </div>
            </div>
            
            <!-- FOOTER -->
            <div class="footer">
                <div class="footer-text">This report is confidential and generated by SQUIFM System</div>
                <div class="footer-text">Â© 2026 Escalona's Farm - All Rights Reserved</div>
            </div>
        </div>
    </body>
    </html>
    `;
}

function generateAnalyticsReport() {
    // Legacy function - kept for compatibility
    return generateProfessionalReport();
}

// Update Analytics Function for Real Data
function updateRealAnalyticsData() {
    console.log('Updating analytics with real data...');
    
    const fromDate = document.getElementById('analyticsFromDate').value;
    const toDate = document.getElementById('analyticsToDate').value;
    
    if (!fromDate || !toDate) {
        console.error('Date inputs not found or empty!');
        return;
    }
    
    // Show loading state
    const loadingElements = [
        'analyticsTotalOrders', 'analyticsTotalRevenue', 'analyticsBestProduct',
        'analyticsOrdersTotal', 'analyticsOrdersCompleted', 'analyticsOrdersPending', 'analyticsOrdersAvg',
        'analyticsEggsSold', 'analyticsLiveQuailSold', 'analyticsDressedQuailSold',
        'analyticsEggsCollected', 'analyticsAvailableEggs', 'analyticsGoodEggs', 'analyticsCrackedEggs', 'analyticsQualityRate'
    ];
    
    loadingElements.forEach(id => {
        const element = document.getElementById(id);
        if (element) element.textContent = 'Loading...';
    });
    
    // Fetch analytics data from API
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
    fetch(`/api/analytics?from_date=${fromDate}&to_date=${toDate}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(result => {
        if (result.success && result.data) {
            const data = result.data;

            console.log('Analytics data received:', data);
            console.log('Daily orders:', data.daily_orders);
            console.log('Product sales:', data.product_sales);
            console.log('Daily feedings:', data.daily_feedings);
            console.log('Total revenue from API:', data.total_revenue);
            console.log('Egg collection data:', {
                total_eggs_collected: data.total_eggs_collected,
                total_good_eggs: data.total_good_eggs,
                total_cracked_eggs: data.total_cracked_eggs,
                quality_rate: data.quality_rate
            });
            
            // Update overview metrics
            updateElement('analyticsTotalOrders', `${data.total_orders} orders`);
            updateElement('analyticsTotalRevenue', `₱${data.total_revenue.toLocaleString()}.00`);
            updateElement('analyticsBestProduct', data.best_product);
            updateElement('analyticsActiveDays', `${data.active_days} days`);
            
            // Update orders analytics
            updateElement('analyticsOrdersTotal', `${data.total_orders} orders`);
            updateElement('analyticsOrdersCompleted', `${data.completed_orders} orders`);
            updateElement('analyticsOrdersPending', `${data.pending_orders} orders`);
            updateElement('analyticsOrdersAvg', `${data.avg_orders_per_day} orders/day`);
            
            // Update sales analytics
            updateElement('analyticsEggsSold', `${data.product_sales.quail_eggs} trays`);
            updateElement('analyticsLiveQuailSold', `${data.product_sales.live_quail} pcs`);
            updateElement('analyticsDressedQuailSold', `${data.product_sales.dressed_quail} pcs`);
            
            // Update egg collection analytics
            updateElement('analyticsEggsCollected', `${data.total_eggs_collected} eggs`);
            updateElement('analyticsAvailableEggs', `${data.available_eggs} eggs`);
            updateElement('analyticsGoodEggs', `${data.total_good_eggs} eggs`);
            updateElement('analyticsCrackedEggs', `${data.total_cracked_eggs} eggs`);
            updateElement('analyticsQualityRate', `${data.quality_rate}%`);

            // Update current farm quail statistics from the same source of truth as inventory
            if (data.farm_quail_stats) {
                if (window.quailInventoryState) {
                    window.quailInventoryState.live_quails = data.farm_quail_stats.live_quails;
                    window.quailInventoryState.dressed_quails_stock = data.farm_quail_stats.dressed_quails;
                }

                const farmLive = data.farm_quail_stats.live_quails ?? 0;
                const farmDead = data.farm_quail_stats.dead_quails ?? 0;
                const farmTotal = data.farm_quail_stats.total_raised ?? (farmLive + farmDead);
                const farmMortality = data.farm_quail_stats.mortality_rate ?? 0;

                updateElement('farmStatsLiveQuails', farmLive);
                updateElement('farmStatsDeadQuails', farmDead);
                updateElement('farmStatsTotalQuails', farmTotal);
                updateElement('farmStatsMortalityRate', `${farmMortality}%`);
                updateElement('liveQuailsDisplay', farmLive);
                updateElement('deadQuailsDisplay', farmDead);
                updateElement('totalQuailsDisplay', farmTotal);
                updateElement('mortalityRateDisplay', `${farmMortality}%`);
                updateElement(
                    'survivalRateDisplay',
                    `${data.farm_quail_stats.survival_rate ?? (100 - farmMortality)}%`
                );
                updateElement(
                    'dressedQuailsDisplay',
                    data.farm_quail_stats.dressed_quails ?? 0
                );
            }
            
            // Update charts
            updateRealCharts(data);
            
            // Update product sales table
            updateProductSalesTable(
                data.product_sales,
                data.product_revenue,
                data.total_revenue
            );
            
            console.log('Analytics updated successfully with real data!');

        } else {
            throw new Error(result.message || 'Failed to fetch analytics data');
        }
    })
    .catch(error => {
        console.error('Error fetching analytics data:', error);
        
        // Show error state
        loadingElements.forEach(id => {
            const element = document.getElementById(id);
            if (element) element.textContent = 'Error loading';
        });
        
        // Show fallback message
        if (typeof addNotification === 'function') {
            addNotification(
                '?',
                'Failed to load analytics data. Please try again.',
                'error'
            );
        } else {
            alert(
                'Failed to load analytics data. Please check the browser console for more details.'
            );
        }
    });
}
// Update Daily Orders Chart
function updateDailyOrdersChart(dailyOrders) {
    const ctx = document.getElementById('dailyOrdersChart');
    if (!ctx) return;
    
    // Initialize charts registry if not exists
    if (!window.squifmCharts) {
        window.squifmCharts = {};
    }
    
    // Destroy existing chart
    if (window.squifmCharts.dailyOrdersChart) {
        window.squifmCharts.dailyOrdersChart.destroy();
    }
    
    const labels = dailyOrders.map(d => d.date);
    const ordersData = dailyOrders.map(d => d.orders);
    const completedData = dailyOrders.map(d => d.completed);
    
    // Check if there's any data
    const hasData = ordersData.some(value => value > 0) || completedData.some(value => value > 0);
    
    window.squifmCharts.dailyOrdersChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Total Orders',
                    data: ordersData,
                    borderColor: '#a1887f',
                    backgroundColor: 'rgba(161, 136, 127, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Completed Orders',
                    data: completedData,
                    borderColor: '#4caf50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    enabled: hasData
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#efebe9' },
                    ticks: { color: '#6d4c41' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#6d4c41' }
                }
            }
        }
    });
    
    // Add "No data" message if no data
    if (!hasData) {
        const chartContainer = ctx.parentElement;
        if (!chartContainer.querySelector('.no-data-message')) {
            const noDataDiv = document.createElement('div');
            noDataDiv.className = 'no-data-message';
            noDataDiv.style.cssText = 'position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #a1887f; font-size: 1rem; text-align: center; pointer-events: none;';
            noDataDiv.innerHTML = 'No orders data yet<br><small>Orders will appear here when customers place orders</small>';
            chartContainer.style.position = 'relative';
            chartContainer.appendChild(noDataDiv);
        }
    } else {
        // Remove no data message if data exists
        const chartContainer = ctx.parentElement;
        const noDataMsg = chartContainer.querySelector('.no-data-message');
        if (noDataMsg) noDataMsg.remove();
    }
}

// Update Product Sales Chart
function updateProductSalesChart(productSales) {
    const ctx = document.getElementById('productSalesChart');
    if (!ctx) return;
    
    // Initialize charts registry if not exists
    if (!window.squifmCharts) {
        window.squifmCharts = {};
    }
    
    // Destroy existing chart
    if (window.squifmCharts.productSalesChart) {
        window.squifmCharts.productSalesChart.destroy();
    }
    
    const data = [
        productSales.quail_eggs || 0,
        productSales.live_quail || 0,
        productSales.dressed_quail || 0
    ];
    
    // Check if there's any sales data
    const hasData = data.some(value => value > 0);
    
    window.squifmCharts.productSalesChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Quail Eggs', 'Live Quail', 'Dressed Quail'],
            datasets: [{
                data: hasData ? data : [1, 1, 1], // Show equal segments if no data
                backgroundColor: hasData ? ['#a1887f', '#8d6e63', '#6d4c41'] : ['#e0e0e0', '#e0e0e0', '#e0e0e0'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: { size: 12 }
                    }
                },
                tooltip: {
                    enabled: hasData,
                    callbacks: {
                        label: function(context) {
                            if (!hasData) return '';
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const unit = label.includes('Eggs') ? ' trays' : ' pcs';
                            return label + ': ' + value + unit;
                        }
                    }
                }
            }
        }
    });
    
    // Add "No data" message if no sales data
    if (!hasData) {
        const chartContainer = ctx.parentElement;
        if (!chartContainer.querySelector('.no-data-message')) {
            const noDataDiv = document.createElement('div');
            noDataDiv.className = 'no-data-message';
            noDataDiv.style.cssText = 'position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #a1887f; font-size: 1rem; text-align: center; pointer-events: none;';
            noDataDiv.innerHTML = 'No sales recorded yet<br><small>Record sales in "Record Sales" tab to see data here</small>';
            chartContainer.style.position = 'relative';
            chartContainer.appendChild(noDataDiv);
        }
    } else {
        // Remove no data message if data exists
        const chartContainer = ctx.parentElement;
        const noDataMsg = chartContainer.querySelector('.no-data-message');
        if (noDataMsg) noDataMsg.remove();
    }
}



// Update Daily Collection Chart
function updateDailyCollectionChart(dailyCollections) {
    const ctx = document.getElementById('dailyCollectionChart');
    if (!ctx) return;
    
    // Initialize charts registry if not exists
    if (!window.squifmCharts) {
        window.squifmCharts = {};
    }
    
    // Destroy existing chart
    if (window.squifmCharts.dailyCollectionChart) {
        window.squifmCharts.dailyCollectionChart.destroy();
    }
    
    const labels = dailyCollections.map(d => d.date);
    const totalEggsData = dailyCollections.map(d => d.total_eggs);
    const goodEggsData = dailyCollections.map(d => d.good_eggs);
    const crackedEggsData = dailyCollections.map(d => d.cracked_eggs);
    
    // Check if there's any collection data
    const hasData = totalEggsData.some(value => value > 0);
    
    window.squifmCharts.dailyCollectionChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Total Eggs',
                    data: totalEggsData,
                    borderColor: '#a1887f',
                    backgroundColor: 'rgba(161, 136, 127, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Good Eggs',
                    data: goodEggsData,
                    borderColor: '#4caf50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.4
                },
                {
                    label: 'Cracked Eggs',
                    data: crackedEggsData,
                    borderColor: '#ff5722',
                    backgroundColor: 'rgba(255, 87, 34, 0.1)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    enabled: hasData
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#efebe9' },
                    ticks: { color: '#6d4c41' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#6d4c41' }
                }
            }
        }
    });
    
    // Add "No data" message if no collection data
    if (!hasData) {
        const chartContainer = ctx.parentElement;
        if (!chartContainer.querySelector('.no-data-message')) {
            const noDataDiv = document.createElement('div');
            noDataDiv.className = 'no-data-message';
            noDataDiv.style.cssText = 'position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #a1887f; font-size: 1rem; text-align: center; pointer-events: none;';
            noDataDiv.innerHTML = 'No egg collections yet<br><small>Collect eggs in "Collect & Inspect Eggs" tab to see data here</small>';
            chartContainer.style.position = 'relative';
            chartContainer.appendChild(noDataDiv);
        }
    } else {
        // Remove no data message if data exists
        const chartContainer = ctx.parentElement;
        const noDataMsg = chartContainer.querySelector('.no-data-message');
        if (noDataMsg) noDataMsg.remove();
    }
}

// Update Product Sales Table
function updateProductSalesTable(productSales, productRevenue, totalRevenue) {
    const tableBody = document.getElementById('productSalesTableBody');
    if (!tableBody) return;
    
    const summaryEl = document.getElementById('salesPerformanceSummary');
    
    const products = [
        {
            name: 'Quail Eggs',
            quantity: productSales.quail_eggs || 0,
            unit: 'trays',
            revenue: productRevenue.quail_eggs || 0
        },
        {
            name: 'Live Quail',
            quantity: productSales.live_quail || 0,
            unit: 'pcs',
            revenue: productRevenue.live_quail || 0
        },
        {
            name: 'Dressed Quail',
            quantity: productSales.dressed_quail || 0,
            unit: 'pcs',
            revenue: productRevenue.dressed_quail || 0
        }
    ];
    
    // Sort by revenue (descending)
    products.sort((a, b) => b.revenue - a.revenue);
    
    // Check if there's any actual sales data
    const hasData = products.some(product => product.quantity > 0);
    
    if (!hasData || totalRevenue === 0) {
        if (summaryEl) {
            summaryEl.textContent = 'Based on Record Sales entries. No sales recorded yet.';
        }
        
        tableBody.innerHTML = `
            <tr>
                <td colspan="4" style="padding: 2rem; text-align: center; color: #a1887f;">
                    No sales recorded yet. Record sales in the "Record Sales" tab to see data here.
                </td>
            </tr>
        `;
        return;
    }

    const topProduct = products[0];
    
    if (summaryEl) {
        summaryEl.textContent = `Top seller: ${topProduct.name} | Total revenue: ₱${Number(totalRevenue).toLocaleString()}.00`;
    }
    
    tableBody.innerHTML = products.map((product, index) => {
        const percentage = totalRevenue > 0
            ? Math.round((product.revenue / totalRevenue) * 100)
            : 0;
        
        return `
            <tr ${index % 2 === 1 ? 'style="background: #f9f9f9;"' : ''}>
                <td style="padding: 1rem; font-size: 0.9rem; color: #000;">
                    ${product.name}
                </td>
                <td style="padding: 1rem; font-size: 0.9rem; color: #000;">
                    ${product.quantity} ${product.unit}
                </td>
                <td style="padding: 1rem; font-size: 0.9rem; color: #000; font-weight: 700;">
                    ₱${Number(product.revenue).toLocaleString()}.00
                </td>
                <td style="padding: 1rem; font-size: 0.9rem; color: #000;">
                    ${percentage}%
                </td>
            </tr>
        `;
    }).join('');
}
// Global function to update analytics (accessible from onclick)
function updateAnalyticsData() {
    updateRealAnalyticsData();
}

// Update Analytics Function for Inventory Page
function updateAnalytics() {
    updateRealAnalyticsData();
}

// Direct analytics update function
function updateAnalyticsDirectly() {
    console.log('=== UPDATING ANALYTICS DIRECTLY ===');
    
    const fromDateInput = document.getElementById('analyticsFromDate');
    const toDateInput = document.getElementById('analyticsToDate');
    
    if (!fromDateInput || !toDateInput) {
        console.error('Date input fields not found!');
        return;
    }
    
    const fromDate = new Date(fromDateInput.value || new Date(Date.now() - 30*24*60*60*1000));
    const toDate = new Date(toDateInput.value || new Date());
    
    console.log('Date range:', fromDate, 'to', toDate);
    
    // Calculate metrics from all records
    let totalEggsCollected = 0;
    let totalRevenue = 0;
    let totalGoodEggs = 0;
    let totalCrackedEggs = 0;
    let eggsSold = 0;
    let liveQuailSold = 0;
    let dressedQuailSold = 0;
    let activeDays = new Set();
    
    // Process collection records
    if (typeof collectionRecords !== 'undefined' && collectionRecords.length > 0) {
        console.log('Processing', collectionRecords.length, 'collection records');
        collectionRecords.forEach(record => {
            const recordDate = new Date(record.timestamp);
            if (recordDate >= fromDate && recordDate <= toDate) {
                totalEggsCollected += record.eggs || 0;
                totalGoodEggs += record.goodEggs || 0;
                totalCrackedEggs += record.crackedEggs || 0;
                activeDays.add(recordDate.toDateString());
            }
        });
    }
    
    // Process sales records
    if (typeof salesRecords !== 'undefined' && salesRecords.length > 0) {
        console.log('Processing', salesRecords.length, 'sales records');
        salesRecords.forEach(record => {
            const recordDate = new Date(record.timestamp);
            if (recordDate >= fromDate && recordDate <= toDate) {
                totalRevenue += record.total || 0;
                if (record.productType === 'eggs') {
                    eggsSold += record.quantity || 0;
                } else if (record.productType === 'live_quail') {
                    liveQuailSold += record.quantity || 0;
                } else if (record.productType === 'dressed_quail') {
                    dressedQuailSold += record.quantity || 0;
                }
                activeDays.add(recordDate.toDateString());
            }
        });
    }
    
    console.log('Calculated metrics:');
    console.log('- Total eggs collected:', totalEggsCollected);
    console.log('- Total revenue:', totalRevenue);
    console.log('- Active days:', activeDays.size);
    
    // Update overview metrics
    const eggsCollectedEl = document.getElementById('analyticsEggsCollected');
    const totalRevenueEl = document.getElementById('analyticsTotalRevenue');
    const activeDaysEl = document.getElementById('analyticsActiveDays');
    const bestProductEl = document.getElementById('analyticsBestProduct');
    
    if (eggsCollectedEl) {
        eggsCollectedEl.textContent = totalEggsCollected + ' eggs';
        console.log('Updated eggs collected:', totalEggsCollected);
    }
    if (totalRevenueEl) {
       totalRevenueEl.textContent = '₱' + totalRevenue.toFixed(2);
        console.log('Updated total revenue:', totalRevenue);
    }
    if (activeDaysEl) {
        activeDaysEl.textContent = activeDays.size + ' days';
        console.log('Updated active days:', activeDays.size);
    }
    
    // Determine best product
    let bestProduct = 'Quail Eggs';
    if (liveQuailSold > eggsSold && liveQuailSold > dressedQuailSold) {
        bestProduct = 'Live Quail';
    } else if (dressedQuailSold > eggsSold && dressedQuailSold > liveQuailSold) {
        bestProduct = 'Dressed Quail';
    }
    if (bestProductEl) bestProductEl.textContent = bestProduct;
    
    // Update production analytics
    const dailyAvg = activeDays.size > 0 ? (totalEggsCollected / activeDays.size).toFixed(1) : 0;
    const productionTotalEl = document.getElementById('analyticsProductionTotal');
    const productionAvgEl = document.getElementById('analyticsProductionAvg');
    const availableEggsEl = document.getElementById('analyticsAvailableEggs');
    const goodEggsEl = document.getElementById('analyticsGoodEggs');
    const crackedEggsEl = document.getElementById('analyticsCrackedEggs');
    const availableEggs = Math.max(0, totalGoodEggs - (eggsSold * 24));
    
    if (productionTotalEl) productionTotalEl.textContent = totalEggsCollected + ' eggs';
    if (productionAvgEl) productionAvgEl.textContent = dailyAvg + ' eggs/day';
    if (availableEggsEl) availableEggsEl.textContent = availableEggs + ' eggs';
    if (goodEggsEl) goodEggsEl.textContent = totalGoodEggs + ' eggs';
    if (crackedEggsEl) crackedEggsEl.textContent = totalCrackedEggs + ' eggs';
    
    // Update sales analytics
    const totalSalesEl = document.getElementById('analyticsTotalSales');
    const eggsSoldEl = document.getElementById('analyticsEggsSold');
    const liveQuailSoldEl = document.getElementById('analyticsLiveQuailSold');
    const dressedQuailSoldEl = document.getElementById('analyticsDressedQuailSold');
    
   if (totalSalesEl) totalSalesEl.textContent = '₱' + totalRevenue.toFixed(2);
    if (eggsSoldEl) eggsSoldEl.textContent = eggsSold + ' trays';
    if (liveQuailSoldEl) liveQuailSoldEl.textContent = liveQuailSold + ' pcs';
    if (dressedQuailSoldEl) dressedQuailSoldEl.textContent = dressedQuailSold + ' pcs';
    
    // Update charts
    console.log('Updating charts...');
    try {
        updateBestSellingChart(eggsSold, liveQuailSold, dressedQuailSold);
        updateDailyProductionChart(fromDate, toDate);
        console.log('Charts updated successfully');
    } catch (error) {
        console.error('Error updating charts:', error);
    }
    
    console.log('Analytics update completed!');
}

// Update Order Status Function
function updateOrderStatus(orderId, newStatus) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!csrfToken) {
        alert('CSRF token not found. Please refresh the page.');
        return;
    }
    
    fetch(`/orders/${orderId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update status badge color
            const statusBadge = document.querySelector(`.order-card:has([onchange*="updateOrderStatus(${orderId})"]) .order-status`);
            if (statusBadge) {
                statusBadge.className = 'order-status ' + newStatus;
                statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                
                // Update colors based on status
                if (newStatus === 'pending') {
                    statusBadge.style.background = '#fff3e0';
                    statusBadge.style.color = '#e65100';
                } else if (newStatus === 'confirmed') {
                    statusBadge.style.background = '#e3f2fd';
                    statusBadge.style.color = '#1565c0';
                } else if (newStatus === 'shipped') {
                    statusBadge.style.background = '#f3e5f5';
                    statusBadge.style.color = '#7b1fa2';
                } else if (newStatus === 'completed') {
                    statusBadge.style.background = '#e8f5e9';
                    statusBadge.style.color = '#2e7d32';
                } else if (newStatus === 'cancelled') {
                    statusBadge.style.background = '#ffebee';
                    statusBadge.style.color = '#c62828';
                }
            }
            
            // Show notification
            if (typeof addNotification === 'function') {
                addNotification('', `Order #${orderId} status updated to "${newStatus}"`);
            } else {
                alert(`Order status updated to ${newStatus}`);
            }
        } else {
            alert('Error updating order status: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating order status. Please try again.');
    });
}

// Product Management Functions - MOVED TO TOP FOR GLOBAL ACCESS
let currentEditProductId = null;
let currentEditType = null;

// Use default products when API fails
function useDefaultProducts() {
    console.log('Using default products...');
    const defaultProducts = [
        { id: 1, name: 'Fresh Quail Eggs', unit: 'tray (24 pieces)', price: 80, stock: 50 },
        { id: 2, name: 'Live Quail', unit: 'piece', price: 180, stock: 25 },
        { id: 3, name: 'Dressed Quail', unit: 'piece (cleaned)', price: 250, stock: 15 }
    ];
    renderProductsManagement(defaultProducts);
}

// Load products for management
async function loadProductsManagement() {
    console.log('Loading products for management...');
    
    try {
        const response = await fetch('/api/products');
        console.log('API Response status:', response.status);
        
        if (!response.ok) {
            console.error('API response not ok:', response.status, response.statusText);
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const products = await response.json();
        console.log('Products loaded:', products);
        
        if (!products || products.length === 0) {
            console.log('No products found, using defaults');
            useDefaultProducts();
            return;
        }
        
        renderProductsManagement(products);
    } catch (error) {
        console.error('Error loading products:', error);
        console.log('Using default products due to error');
        useDefaultProducts();
    }
}

// Render products management grid
function renderProductsManagement(products) {
    console.log('Rendering products:', products);
    const grid = document.getElementById('productsManagementGrid');
    if (!grid) {
        console.error('Products management grid not found!');
        return;
    }
    
    if (!products || products.length === 0) {
        grid.innerHTML = '<div style="text-align: center; padding: 2rem; color: #8d6e63;">No products found. Please add products to the database.</div>';
        return;
    }
    
    grid.innerHTML = products.map(product => {
        const stockClass = getStockClass(product.stock);
        const stockText = product.stock === 0 ? 'Out of Stock' : 
                         product.stock <= 5 ? 'Low Stock' : 'In Stock';
        
        return `
            <div class="product-management-card">
                <div class="product-header">
                    <div>
                        <div class="product-name">${product.name}</div>
                        <div class="product-unit">Per ${product.unit}</div>
                    </div>
                    <div class="stock-badge ${stockClass}">
                        ${stockText}: ${product.stock}
                    </div>
                </div>
                
               <div class="product-controls">
                    <div class="price-display" onclick="editProductPrice(${product.id}, ${product.price})" title="Click to edit price">
                        ₱${parseFloat(product.price).toFixed(2)}
                        <div style="font-size: 0.7rem; opacity: 0.8; margin-top: 0.25rem;">Click to edit</div>
                    </div>
                    <div class="stock-display" onclick="editProductStock(${product.id}, ${product.stock})" title="Click to edit stock">
                        Stock: ${product.stock}
                        <div style="font-size: 0.7rem; opacity: 0.8; margin-top: 0.25rem;">Click to edit</div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
    
    console.log('Products rendered successfully');
}

// Get stock class for styling
function getStockClass(stock) {
    if (stock === 0) return 'out';
    if (stock <= 5) return 'low';
    return '';
}

// Edit product price
function editProductPrice(productId, currentPrice) {
    currentEditProductId = productId;
    currentEditType = 'price';
    document.getElementById('priceEditInput').value = currentPrice;
    document.getElementById('priceEditModal').classList.add('show');
}

// Edit product stock
function editProductStock(productId, currentStock) {
    currentEditProductId = productId;
    currentEditType = 'stock';
    document.getElementById('stockEditInput').value = currentStock;
    document.getElementById('stockEditModal').classList.add('show');
}

// Close edit modal
function closeEditModal(modalId) {
    document.getElementById(modalId).classList.remove('show');
    currentEditProductId = null;
    currentEditType = null;
}

// Save price edit
async function savePriceEdit() {
    const newPrice = document.getElementById('priceEditInput').value;
    
    if (!newPrice || newPrice <= 0) {
        alert('Please enter a valid price');
        return;
    }
    
    try {
        console.log('Saving price for product ID:', currentEditProductId, 'New price:', newPrice);
        
        const response = await fetch(`/products/${currentEditProductId}/price`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ price: newPrice })
        });
        
        console.log('Response status:', response.status);
        console.log('Content-Type:', response.headers.get('content-type'));
        
        const responseText = await response.text();
        console.log('Raw response:', responseText);
        
        if (!responseText) {
            alert('Error: Server returned an empty response');
            return;
        }
        
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (e) {
            console.error('Failed to parse JSON response:', responseText);
            alert('Server error: ' + responseText.substring(0, 200));
            return;
        }
        
        console.log('Response result:', result);
                
            if (response.ok && result.success) {
            closeEditModal('priceEditModal');
            loadProductsManagement(); // Reload products
            addNotification('', `Price updated to ₱${parseFloat(newPrice).toFixed(2)}`);
        } else if (response.status === 404) {
            alert('Product not found. Make sure the product exists in the database.');
        } else if (response.status === 401) {
            alert('Not authorized. Please log in again.');
        } else {
            alert('Error updating price: ' + (result?.message || `Server error (${response.status})`));
        }
            } catch (error) {
                console.error('Error:', error);
                alert('Error updating price: ' + error.message);
            }
}

// Save stock edit
async function saveStockEdit() {
    const newStock = document.getElementById('stockEditInput').value;
    
    if (newStock === '' || newStock < 0) {
        alert('Please enter a valid stock quantity');
        return;
    }
    
    try {
        console.log('Saving stock for product ID:', currentEditProductId, 'New stock:', newStock);
        
        const response = await fetch(`/products/${currentEditProductId}/stock`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ stock: newStock })
        });
        
        console.log('Response status:', response.status);
        console.log('Content-Type:', response.headers.get('content-type'));
        
        const responseText = await response.text();
        console.log('Raw response:', responseText);
        
        if (!responseText) {
            alert('Error: Server returned an empty response');
            return;
        }
        
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (e) {
            console.error('Failed to parse JSON response:', responseText);
            alert('Server error: ' + responseText.substring(0, 200));
            return;
        }
        
        console.log('Response result:', result);
        
        if (response.ok && result.success) {
            closeEditModal('stockEditModal');
            loadProductsManagement(); // Reload products
            addNotification('', `Stock updated to ${newStock} units`);
        } else if (response.status === 404) {
            alert('Product not found. Make sure the product exists in the database.');
        } else if (response.status === 401) {
            alert('Not authorized. Please log in again.');
        } else {
            alert('Error updating stock: ' + (result?.message || `Server error (${response.status})`));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error updating stock: ' + error.message);
    }
}

// Temperature & Humidity Modal Functions
function showTemperatureHumidity() {
    const modal = document.getElementById('temperatureHumidityModal');
    if (modal) {
        modal.classList.add('show');
        updateTemperatureHumidityData();
    }
}

function closeTemperatureHumidity() {
    const modal = document.getElementById('temperatureHumidityModal');
    if (modal) modal.classList.remove('show');
}

function updateTemperatureHumidityData() {
    const temp = Math.floor(Math.random() * 15) + 18;
    const humidity = Math.floor(Math.random() * 30) + 50;
    document.getElementById('current-temp').textContent = temp + 'C';
    document.getElementById('current-humidity').textContent = humidity + '%';
    let advice = '';
    if (temp < 18) advice = 'Temperature too low! Consider adding heating for adult quail.';
    else if (temp > 24 && temp <= 30) advice = 'Temperature slightly high. Ensure good ventilation.';
    else if (temp > 30) advice = 'Temperature too high! Immediate cooling needed to prevent heat stress.';
    if (humidity < 50) advice += (advice ? ' ' : '') + 'Humidity too low. Consider adding water sources or humidifiers.';
    else if (humidity > 70) advice += (advice ? ' ' : '') + 'Humidity too high. Improve ventilation to prevent respiratory issues.';
    if (!advice) advice = 'Temperature and humidity levels are optimal for quail farming.';
    document.getElementById('temp-humidity-advice').textContent = advice;
}

// Add a function to manually refresh analytics when switching to analytics tab
// --------------------------------------------------------------------------------
// NEW ANALYTICS SECTIONS - Inventory Status, YTD, Expenses, Profit, Monthly, Customers
// --------------------------------------------------------------------------------



// Update Helper function
function updateElement(id, value) {
    const element = document.getElementById(id);
    if (element) element.textContent = value;
}

// --------------------------------------------------------------------------------

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
    localStorage.setItem('inventoryActiveTab', categoryId);
    
    // If switching to analytics tab, refresh the data with real API data
    if (categoryId === 'analytics') {
        console.log('Switching to analytics tab - refreshing with real data...');
        setTimeout(() => {
            updateRealAnalyticsData();
        }, 200);
    }
    
    // If switching to orders tab, load products
    if (categoryId === 'orders') {
        console.log('Switching to orders tab - loading products...');
        setTimeout(() => {
            loadProductsManagement();
        }, 200);
    }
    
    // Scroll to top
    window.scrollTo({ top: 200, behavior: 'smooth' });
}

// Restore active tab from localStorage on page load for inventory
(function() {
    const savedTab = localStorage.getItem('inventoryActiveTab');
    
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

// Sales Functions
const salesProductPrices = {
    eggs: 160,
    live_quail: 180,
    dressed_quail: 250
};

function updateSalesPrice(selectId = 'salesProductType', priceInputId = 'salesPrice') {
    const select = document.getElementById(selectId);
    const priceInput = document.getElementById(priceInputId);

    if (!select || !priceInput) {
        return;
    }

    const price = salesProductPrices[select.value] ?? select.selectedOptions[0]?.getAttribute('data-price') ?? '';
    priceInput.value = price;

    if (priceInputId === 'salesPrice') {
        calculateSalesTotal();
    }
}

function calculateSalesTotal() {
    const quantity = parseFloat(document.getElementById('salesQuantity').value) || 0;
    const price = parseFloat(document.getElementById('salesPrice').value) || 0;
    const total = quantity * price;
    document.getElementById('salesTotalAmount').textContent = '₱' + total.toFixed(2);
}

function getQuailStock(productType) {
    if (!window.quailInventoryState) {
        return 0;
    }

    if (productType === 'live_quail') {
        return parseInt(window.quailInventoryState.live_quails, 10) || 0;
    }

    if (productType === 'dressed_quail') {
        return parseInt(window.quailInventoryState.dressed_quails_stock, 10) || 0;
    }

    return 0;
}

function updateQuailStockDisplay() {
    const liveQuailsDisplay = document.getElementById('liveQuailsDisplay');
    const dressedQuailsDisplay = document.getElementById('dressedQuailsDisplay');
    const totalQuailsDisplay = document.getElementById('totalQuailsDisplay');
    const mortalityRateDisplay = document.getElementById('mortalityRateDisplay');
    const survivalRateDisplay = document.getElementById('survivalRateDisplay');
    const farmStatsLiveQuails = document.getElementById('farmStatsLiveQuails');
    const farmStatsDeadQuails = document.getElementById('farmStatsDeadQuails');
    const farmStatsTotalQuails = document.getElementById('farmStatsTotalQuails');
    const farmStatsMortalityRate = document.getElementById('farmStatsMortalityRate');

    if (liveQuailsDisplay) liveQuailsDisplay.textContent = window.quailInventoryState.live_quails;
    if (dressedQuailsDisplay) dressedQuailsDisplay.textContent = window.quailInventoryState.dressed_quails_stock;
    const liveQuails = parseInt(window.quailInventoryState.live_quails, 10) || 0;
    const deadQuails = parseInt(document.getElementById('deadQuailsDisplay')?.textContent || '0', 10) || 0;
    const totalQuails = liveQuails + deadQuails;
    const mortalityRate = totalQuails > 0 ? Math.round((deadQuails / totalQuails) * 10000) / 100 : 0;

    if (totalQuailsDisplay) totalQuailsDisplay.textContent = totalQuails;
    if (farmStatsLiveQuails) farmStatsLiveQuails.textContent = liveQuails;
    if (farmStatsDeadQuails) farmStatsDeadQuails.textContent = deadQuails;
    if (farmStatsTotalQuails) farmStatsTotalQuails.textContent = totalQuails;
    if (farmStatsMortalityRate) farmStatsMortalityRate.textContent = mortalityRate + '%';

    if (mortalityRateDisplay) mortalityRateDisplay.textContent = mortalityRate + '%';
    if (survivalRateDisplay) survivalRateDisplay.textContent = (100 - mortalityRate) + '%';
}

async function syncQuailStockAfterSale(productType, quantity) {
    const response = await fetch('/api/quail-stock/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: JSON.stringify({
            product_type: productType,
            quantity: quantity
        })
    });

    const result = await response.json();

    if (!response.ok || !result.success) {
        throw new Error(result.message || `Stock update failed (${response.status})`);
    }

    window.quailInventoryState.live_quails = result.data.live_quails;
    window.quailInventoryState.dressed_quails_stock = result.data.dressed_quails_stock;
    updateQuailStockDisplay();
    return result;
}

// Initialize everything when page loads
function initializePage() {
    // Set current time for collection time input
    const now = new Date();
    const currentTime = now.toTimeString().slice(0, 5); // HH:MM format
    const collectTimeInput = document.getElementById('collectTime');
    if (collectTimeInput) {
        collectTimeInput.value = currentTime;
    }
    
    // Set default values for form fields
    const collectCagePen = document.getElementById('collectCagePen');
    const collectEggSize = document.getElementById('collectEggSize');
    const collectFeedBrand = document.getElementById('collectFeedBrand');
    
    if (collectCagePen) {
        collectCagePen.value = '1'; // Default to Cage/Pen 1
    }
    
    if (collectEggSize) {
        collectEggSize.value = 'mixed'; // Default to Mixed Sizes
    }
    
    if (collectFeedBrand) {
        collectFeedBrand.value = 'quail_layer_smash'; // Default to Quail Layer Smash
    }
    
    // Set current date for production date input
    const currentDate = now.toISOString().split('T')[0]; // YYYY-MM-DD format
    const productionDateInput = document.getElementById('productionDate');
    if (productionDateInput) {
        productionDateInput.value = currentDate;
    }
    
    // Load daily data from localStorage
    loadDailyData();
    
    // Load collection records
    loadCollectionRecords();
    
    // Load production records
    loadProductionRecords();
    
    // Load inspection records
    loadInspectionRecords();
    
    // Load classification records
    loadClassificationRecords();
    
    // Load sales records
    loadSalesRecords();
    
    // Force render sales table after loading
    setTimeout(() => {
        renderSalesTable();
        updateSalesCounters();
        if (document.getElementById('analyticsFromDate')) {
            updateAnalytics();
        }
    }, 500);
    
    // Initialize analytics with REAL DATA from API
    setTimeout(() => {
        // Only initialize if we're on the analytics tab or if analytics elements exist
        if (document.getElementById('analyticsFromDate')) {
            updateRealAnalyticsData();
        }
    }, 1000); // Wait 1 second to ensure all data is loaded
}

// Add event listeners for sales calculation
document.addEventListener('DOMContentLoaded', function() {
    const qtyInput = document.getElementById('salesQuantity');
    const priceInput = document.getElementById('salesPrice');
    if (qtyInput) qtyInput.addEventListener('input', calculateSalesTotal);
    if (priceInput) priceInput.addEventListener('input', calculateSalesTotal);
    
    const confirmBtn = document.getElementById('confirmModalYes');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            executeConfirm();
        });
    }
    
    // Close modal on overlay click
    const overlay = document.getElementById('confirmModal');
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) closeConfirmModal();
        });
    }
    
    // Initialize page after a short delay to ensure DOM is fully ready
    setTimeout(initializePage, 100);
});

// Print Transactions
function printTransactions() {
    const printContent = document.getElementById('transactions-table').outerHTML;
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Print Transactions</title>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; padding: 20px; }');
    printWindow.document.write('h1 { color: #6d4c41; text-align: center; }');
    printWindow.document.write('table { width: 100%; border-collapse: collapse; margin-top: 20px; }');
    printWindow.document.write('th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }');
    printWindow.document.write('th { background-color: #a1887f; color: white; }');
    printWindow.document.write('tr:nth-child(even) { background-color: #f2f2f2; }');
    printWindow.document.write('</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<h1>Transactions Report</h1>');
    printWindow.document.write('<p>Date: ' + new Date().toLocaleDateString() + '</p>');
    printWindow.document.write(printContent);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

// Call Customer
function callCustomer(phone) {
    if (phone) {
        window.location.href = 'tel:' + phone.replace(/\s/g, '');
    } else {
        alert('No phone number available');
    }
}

// Reusable Confirmation Modal System
let pendingCallback = null;
let pendingOrderId = null;

// Daily Summary Data Tracking
let dailyData = {
    totalCollected: 0,
    goodEggs: 0,
    crackedEggs: 0,
    eggsSold: 0,
    liveQuailSold: 0,
    dressedQuailSold: 0,
    eggsIncome: 0,
    quailIncome: 0,
    activities: []
};

// Load saved data from localStorage
function loadDailyData() {
    const today = new Date().toDateString();
    const saved = localStorage.getItem('dailyData_' + today);
    if (saved) {
        dailyData = JSON.parse(saved);
        updateSummaryDisplay();
        renderActivities();
    }
}

// Save data to localStorage
function saveDailyData() {
    const today = new Date().toDateString();
    localStorage.setItem('dailyData_' + today, JSON.stringify(dailyData));
}

// Update Summary Display
function updateSummaryDisplay() {
    // Check if elements exist before updating (they might not exist on current page)
    const summaryTotalCollected = document.getElementById('summaryTotalCollected');
    const summaryGoodEggs = document.getElementById('summaryGoodEggs');
    const summaryCrackedEggs = document.getElementById('summaryCrackedEggs');
    const summaryEggsSold = document.getElementById('summaryEggsSold');
    const summaryLiveQuail = document.getElementById('summaryLiveQuail');
    const summaryDressedQuail = document.getElementById('summaryDressedQuail');
    const summaryRemainingStock = document.getElementById('summaryRemainingStock');
    const summaryEggsIncome = document.getElementById('summaryEggsIncome');
    const summaryQuailIncome = document.getElementById('summaryQuailIncome');
    const summaryTotalIncome = document.getElementById('summaryTotalIncome');
    
    if (summaryTotalCollected) summaryTotalCollected.textContent = dailyData.totalCollected + ' eggs';
    if (summaryGoodEggs) summaryGoodEggs.textContent = dailyData.goodEggs + ' eggs';
    if (summaryCrackedEggs) summaryCrackedEggs.textContent = dailyData.crackedEggs + ' eggs';
    if (summaryEggsSold) summaryEggsSold.textContent = dailyData.eggsSold + ' trays';
    if (summaryLiveQuail) summaryLiveQuail.textContent = dailyData.liveQuailSold + ' pcs';
    if (summaryDressedQuail) summaryDressedQuail.textContent = dailyData.dressedQuailSold + ' pcs';
    
    const remainingStock = dailyData.goodEggs - (dailyData.eggsSold * 24); // 24 eggs per tray
    if (summaryEggsIncome) summaryEggsIncome.textContent = '₱' + dailyData.eggsIncome.toFixed(2);
    if (summaryQuailIncome) summaryQuailIncome.textContent = '₱' + dailyData.quailIncome.toFixed(2);
    if (summaryTotalIncome) summaryTotalIncome.textContent = '₱' + (dailyData.eggsIncome + dailyData.quailIncome).toFixed(2);
}

// Render Activities Log
function renderActivities() {
    const container = document.getElementById('summaryActivityLog');
    if (!container) {
        console.log('Activity log container not found, skipping render');
        return;
    }
    
    if (dailyData.activities.length === 0) {
        container.innerHTML = '<div class="activity-empty" style="color: #a1887f; text-align: center; padding: 1rem;">No activities recorded yet today.</div>';
        return;
    }
    container.innerHTML = dailyData.activities.map(act => `
        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 0; border-bottom: 1px solid #efe8e3;">
            <span style="font-size: 1.25rem;">${act.icon}</span>
            <div style="flex: 1;">
                <div style="font-weight: 600; color: #4e342e; font-size: 0.9rem;">${act.title}</div>
                <div style="font-size: 0.8rem; color: #8d6e63;">${act.detail}</div>
            </div>
            <span style="font-size: 0.75rem; color: #a1887f;">${act.time}</span>
        </div>
    `).join('');
}

// Add Activity
function addActivity(icon, title, detail) {
    const now = new Date();
    const time = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
    dailyData.activities.unshift({ icon, title, detail, time });
    if (dailyData.activities.length > 20) dailyData.activities.pop(); // Keep last 20
    saveDailyData();
    renderActivities();
}

// Notification storage functions
function getNotifications() {
    try { 
        return JSON.parse(localStorage.getItem('squifm_notifications') || '[]'); 
    } catch { 
        return []; 
    }
}

function saveNotifications(notifications) {
    localStorage.setItem('squifm_notifications', JSON.stringify(notifications));
}

function getSeenCount() {
    return parseInt(localStorage.getItem('squifm_notifications_seen') || '0', 10);
}

function setSeenCount(count) {
    localStorage.setItem('squifm_notifications_seen', count.toString());
}

// Add Notification to Bell
function addNotification(icon, message, type = 'success', clickAction = null) {
    const list = document.getElementById('notification-list');
    const badge = document.getElementById('notification-badge');
    const dropdown = document.getElementById('notification-dropdown');
    
    // Safety check - if notification elements don't exist, just log and return
    if (!list || !badge || !dropdown) {
        console.log('Notification system not available on this page:', message);
        return;
    }

    // Unified sidebar bell owns the dropdown â€” keep history & toast, skip DOM injection
    if (window.__squifmBellFeedActive) {
        const notifications = getNotifications();
        notifications.unshift({ icon, message, type, time: new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }) });
        if (notifications.length > 50) notifications.pop();
        saveNotifications(notifications);
        playNotificationSound();
        if (window.notify) {
            if (type === 'success') window.notify.success(message, icon);
            else if (type === 'warning') window.notify.warning(message, icon);
            else if (type === 'error') window.notify.error(message, icon);
            else window.notify.info(message, icon);
        }
        return;
    }
    
    const emptyMsg = list.querySelector('.notification-empty');
    if (emptyMsg) emptyMsg.remove();
    
    const now = new Date();
    const time = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
    
    // Save to localStorage for persistence
    const notifications = getNotifications();
    notifications.unshift({ icon, message, type, time });
    if (notifications.length > 50) notifications.pop(); // Keep max 50 notifications
    saveNotifications(notifications);
    
    const item = document.createElement('div');
    item.className = 'notification-item ' + type;
    const isOrderNotif = message.includes('Order');
    item.style.cssText = 'display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.75rem; cursor: ' + (clickAction || isOrderNotif ? 'pointer' : 'default') + ';';
    item.innerHTML = `
        <span class="notification-icon">${icon}</span>
        <div class="notification-content">
            <div class="notification-text">${message}</div>
            <div class="notification-time">${time}</div>
        </div>
    `;
    
    // Add click action if provided or if it's an order notification
    if (clickAction) {
        item.onclick = function() {
            clickAction();
            dropdown.classList.remove('show');
        };
    } else if (isOrderNotif) {
        item.onclick = function() {
            goToOrdersTab();
            dropdown.classList.remove('show');
        };
    }
    
    list.insertBefore(item, list.firstChild);
    
    // Update badge
    const currentCount = parseInt(badge.textContent) || 0;
    badge.textContent = currentCount + 1;
    badge.style.display = 'flex';
    badge.classList.add('has-notifications');
    
    // Flash the bell
    const bell = document.getElementById('notification-bell');
    if (bell) {
        bell.style.animation = 'bellShake 0.5s ease';
        setTimeout(() => bell.style.animation = '', 500);
    }
    
    // Play notification sound
    playNotificationSound();
    
    // Also add to centralized notification system
    if (window.notify) {
        if (type === 'success') {
            window.notify.success(message, icon);
        } else if (type === 'warning') {
            window.notify.warning(message, icon);
        } else if (type === 'error') {
            window.notify.error(message, icon);
        } else {
            window.notify.info(message, icon);
        }
    }
    
    // Auto-show dropdown for 3 seconds
    dropdown.classList.add('show');
    setTimeout(() => {
        dropdown.classList.remove('show');
    }, 3000);
}

// Play notification sound
function playNotificationSound() {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.setValueAtTime(880, audioContext.currentTime); // A5 note
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.3);
    } catch (e) {
        console.log('Audio not supported');
    }
}

// Go to Order tab
function goToOrdersTab() {
    // Check if we're on the inventory page
    if (typeof showCategory === 'function') {
        showCategory('orders');
    } else {
        // Redirect to inventory page with orders tab
        window.location.href = '/inventory?tab=orders';
    }
}

// Check URL for tab parameter on page load
function checkUrlForTab() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    if (tab && typeof showCategory === 'function') {
        showCategory(tab);
    }
}

function showConfirmModal(options) {
    document.getElementById('confirmModalIcon').textContent = options.icon || '';
    document.getElementById('confirmModalTitle').textContent = options.title || 'Confirm Action';
    document.getElementById('confirmModalMessage').textContent = options.message || 'Are you sure?';
    document.getElementById('confirmModalYes').textContent = options.buttonText || 'Yes';
    pendingCallback = options.onConfirm || null;
    document.getElementById('confirmModal').classList.add('show');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.remove('show');
    pendingCallback = null;
    pendingOrderId = null;
}

function executeConfirm() {
    if (pendingCallback) {
        const callback = pendingCallback;
        pendingCallback = null; // Clear callback first
        closeConfirmModal(); // Close modal immediately
        callback(); // Execute the callback
    } else {
        closeConfirmModal();
    }
}

// Mark Order as Done
function markOrderDone(orderId) {
    const id = String(orderId);
    
    if (!id || id === 'undefined' || id === 'null') {
        alert('Error: Invalid order ID.');
        return;
    }
    
    showConfirmModal({
        icon: '',
        title: 'Mark as Done',
        message: `Mark order #${id} as done?`,
        buttonText: 'Yes, Done',
        onConfirm: function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            if (!csrfToken) {
                alert('CSRF token not found. Please refresh the page.');
                return;
            }
            
            const btn = document.getElementById('check-btn-' + id);
            if (btn) {
                btn.textContent = '';
                btn.disabled = true;
            }
            
            fetch('/orders/' + id + '/status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: 'done' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const badge = document.getElementById('status-badge-' + id);
                    if (badge) {
                        badge.textContent = ' Done';
                        badge.style.background = '#e8f5e9';
                        badge.style.color = '#2e7d32';
                    }
                    
                    if (btn) {
                        btn.textContent = '';
                        btn.disabled = true;
                        btn.style.opacity = '0.5';
                        btn.style.cursor = 'not-allowed';
                        btn.title = 'Already marked as done';
                    }
                    
                    addNotification('', 'Order marked as done successfully');
                    
                    // Refresh analytics data
                    setTimeout(() => {
                        if (typeof updateRealAnalyticsData === 'function') {
                            updateRealAnalyticsData();
                        }
                    }, 300);
                } else {
                    if (btn) {
                        btn.textContent = '';
                        btn.disabled = false;
                    }
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                if (btn) {
                    btn.textContent = '';
                    btn.disabled = false;
                }
                alert('Error updating order status. Please try again.');
            });
        }
    });
}

// Delete Order
function deleteOrder(orderId) {
    showConfirmModal({
        title: 'Delete Order',
        message: 'Are you sure you want to delete this order? This action cannot be undone.',
        buttonText: 'Yes, Delete',
        onConfirm: function() {
            fetch('/orders/' + orderId + '/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the row from the table
                    const row = document.querySelector(`[data-order-id="${orderId}"]`);
                    if (row) {
                        row.remove();
                    } else {
                        // Fallback: reload the page
                        location.reload();
                    }
                    addNotification('', 'Order deleted successfully');
                } else {
                    alert('Error deleting order: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting order');
            });
        }
    });
}

// Collection Records Management
let collectionRecords = [];
let productionRecords = [];
let inspectionRecords = [];
let classificationRecords = [];
let salesRecords = [];

// Load collection records from localStorage
function loadCollectionRecords() {
    const saved = localStorage.getItem('collectionRecords');
    if (saved) {
        collectionRecords = JSON.parse(saved);
        renderCollectionTable();
    }
}

// Load production records from localStorage
function loadProductionRecords() {
    const saved = localStorage.getItem('productionRecords');
    if (saved) {
        productionRecords = JSON.parse(saved);
        renderProductionTable();
        updateProductionCounters();
    }
}

// Load inspection records from localStorage
function loadInspectionRecords() {
    const saved = localStorage.getItem('inspectionRecords');
    if (saved) {
        inspectionRecords = JSON.parse(saved);
        renderInspectionTable();
        updateInspectionCounters();
    }
}

// Load classification records from localStorage
function loadClassificationRecords() {
    // Skip if classify tab doesn't exist (we removed it)
    if (!document.getElementById('classificationRecordsBody')) {
        console.log('Classification table not found, skipping...');
        return;
    }
    
    const saved = localStorage.getItem('classificationRecords');
    if (saved) {
        classificationRecords = JSON.parse(saved);
        renderClassificationTable();
        updateClassificationCounters();
    }
}

// Load sales records from localStorage
function loadSalesRecords() {
    try {
        const saved = localStorage.getItem('salesRecords');
        
        if (saved && saved !== 'null' && saved !== 'undefined') {
            salesRecords = JSON.parse(saved);
            
            // Force render table immediately
            renderSalesTable();
            updateSalesCounters();
            if (document.getElementById('analyticsFromDate')) {
                updateAnalytics();
            }
        } else {
            salesRecords = [];
            renderSalesTable(); // Render empty table
        }
    } catch (error) {
        salesRecords = [];
        renderSalesTable(); // Render empty table
    }
}

// Save collection records to localStorage
function saveCollectionRecords() {
    localStorage.setItem('collectionRecords', JSON.stringify(collectionRecords));
}

// Save production records to localStorage
function saveProductionRecords() {
    localStorage.setItem('productionRecords', JSON.stringify(productionRecords));
}

// Save inspection records to localStorage
function saveInspectionRecords() {
    localStorage.setItem('inspectionRecords', JSON.stringify(inspectionRecords));
}

// Save classification records to localStorage
function saveClassificationRecords() {
    localStorage.setItem('classificationRecords', JSON.stringify(classificationRecords));
}

// Save sales records to localStorage
function saveSalesRecords() {
    try {
        localStorage.setItem('salesRecords', JSON.stringify(salesRecords));
        console.log('Sales records saved:', salesRecords.length, 'records');
    } catch (error) {
        console.error('Error saving sales records:', error);
    }
}

// Update inspection counters in the info cards
function updateInspectionCounters() {
    const today = new Date().toDateString();
    const todayRecords = inspectionRecords.filter(record => 
        new Date(record.timestamp).toDateString() === today
    );
    
    let totalInspected = 0;
    let goodTotal = 0;
    let crackedTotal = 0;
    
    todayRecords.forEach(record => {
        totalInspected += record.totalInspected;
        goodTotal += record.goodEggs;
        crackedTotal += record.crackedEggs;
    });
    
    // Update the info cards - check if elements exist first
    const infoCards = document.querySelectorAll('#inspect .info-card');
    if (infoCards.length >= 3) {
        const totalCard = infoCards[0].querySelector('.info-card-text');
        const goodCard = infoCards[1].querySelector('.info-card-text');
        const crackedCard = infoCards[2].querySelector('.info-card-text');
        
        if (totalCard) totalCard.textContent = totalInspected + ' eggs';
        if (goodCard) goodCard.textContent = goodTotal + ' eggs';
        if (crackedCard) crackedCard.textContent = crackedTotal + ' eggs';
    }
}

// Update classification counters in the info cards
function updateClassificationCounters() {
    // Skip if classify tab doesn't exist (we removed it)
    const infoCards = document.querySelectorAll('#classify .info-card');
    if (infoCards.length === 0) {
        console.log('Classification info cards not found, skipping counter update...');
        return;
    }
    
    const today = new Date().toDateString();
    const todayRecords = classificationRecords.filter(record => 
        new Date(record.timestamp).toDateString() === today
    );
    
    let goodTotal = 0;
    let crackedTotal = 0;
    
    todayRecords.forEach(record => {
        goodTotal += record.goodEggs;
        crackedTotal += record.crackedEggs;
    });
    
    // Update the info cards - check if elements exist first
    if (infoCards.length >= 2) {
        const goodCard = infoCards[0].querySelector('.info-card-text');
        const crackedCard = infoCards[1].querySelector('.info-card-text');
        
        if (goodCard) goodCard.textContent = goodTotal + ' eggs';
        if (crackedCard) crackedCard.textContent = crackedTotal + ' eggs';
    }
}

// Update sales counters in the info cards
function updateSalesCounters() {
    const today = new Date().toDateString();
    const todayRecords = salesRecords.filter(record => 
        new Date(record.timestamp).toDateString() === today
    );
    
    let eggsSold = 0;
    let liveQuailSold = 0;
    let dressedQuailSold = 0;
    let totalIncome = 0;
    
    todayRecords.forEach(record => {
        if (record.productType === 'eggs') {
            eggsSold += record.quantity;
        } else if (record.productType === 'live_quail') {
            liveQuailSold += record.quantity;
        } else if (record.productType === 'dressed_quail') {
            dressedQuailSold += record.quantity;
        }
        totalIncome += record.total;
    });
    
    // Update the info cards - check if elements exist first
    const infoCards = document.querySelectorAll('#sales .info-card');
    if (infoCards.length >= 4) {
        const eggsCard = infoCards[0].querySelector('.info-card-text');
        const liveCard = infoCards[1].querySelector('.info-card-text');
        const dressedCard = infoCards[2].querySelector('.info-card-text');
        const incomeCard = infoCards[3].querySelector('.info-card-text');
        
        if (eggsCard) eggsCard.textContent = eggsSold + ' trays';
        if (liveCard) liveCard.textContent = liveQuailSold + ' pcs';
        if (dressedCard) dressedCard.textContent = dressedQuailSold + ' pcs';
        if (incomeCard) incomeCard.textContent = '₱' + totalIncome.toFixed(2);
    }
}
function updateProductionCounters() {
    const today = new Date().toDateString();
    const todayRecords = productionRecords.filter(record => 
        new Date(record.timestamp).toDateString() === today
    );
    
    let totalToday = 0;
    let cage1Total = 0;
    let cage2Total = 0;
    let cage3Total = 0;
    
    todayRecords.forEach(record => {
        totalToday += record.totalEggs;
        cage1Total += record.cage1 || 0;
        cage2Total += record.cage2 || 0;
        cage3Total += record.cage3 || 0;
    });
    
    // Update the info cards - check if elements exist first
    const infoCards = document.querySelectorAll('#record .info-card');
    if (infoCards.length >= 4) {
        const totalCard = infoCards[0].querySelector('.info-card-text');
        const cage1Card = infoCards[1].querySelector('.info-card-text');
        const cage2Card = infoCards[2].querySelector('.info-card-text');
        const cage3Card = infoCards[3].querySelector('.info-card-text');
        
        if (totalCard) totalCard.textContent = totalToday + ' eggs';
        if (cage1Card) cage1Card.textContent = cage1Total + ' eggs';
        if (cage2Card) cage2Card.textContent = cage2Total + ' eggs';
        if (cage3Card) cage3Card.textContent = cage3Total + ' eggs';
    }
}

// Render production table
function renderProductionTable() {
    const tbody = document.getElementById('productionRecordsBody');
    if (productionRecords.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" style="padding: 2rem; text-align: center; color: #000;">No production records yet. Start recording production to see records here.</td></tr>';
        return;
    }
    
    tbody.innerHTML = productionRecords.map((record, index) => `
        <tr style="border-bottom: 1px solid #efebe9;">
            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${record.date}</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${record.time}</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${record.cage1 || 0} eggs</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${record.cage2 || 0} eggs</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${record.cage3 || 0} eggs</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #000; font-weight: 700;">${record.totalEggs} eggs</td>
            <td style="padding: 1rem;">
                <span style="display: inline-block; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; background: #e8f5e9; color: #2e7d32;">
                     Recorded
                </span>
            </td>
            <td style="padding: 1rem;">
                <div style="display: flex; gap: 0.5rem;">
                    <button class="action-btn btn-update" onclick="editProductionRecord(${index})"> Update</button>
                    <button class="action-btn btn-delete" onclick="deleteProductionRecord(${index})"> Delete</button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Render inspection table
function renderInspectionTable() {
    const tbody = document.getElementById('inspectionRecordsBody');
    if (!tbody) {
        console.log('Inspection table not found, skipping...');
        return;
    }
    if (inspectionRecords.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="padding: 2rem; text-align: center; color: #000;">No inspection records yet. Start inspecting eggs to see records here.</td></tr>';
        return;
    }
    
    tbody.innerHTML = inspectionRecords.map((record, index) => `
        <tr style="border-bottom: 1px solid #efebe9;">
            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${record.date}</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${record.time}</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #000; font-weight: 700;">${record.totalInspected} eggs</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${record.goodEggs} eggs</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${record.crackedEggs} eggs</td>
            <td style="padding: 1rem;">
                <span style="display: inline-block; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; background: #e8f5e9; color: #2e7d32;">
                     Inspected
                </span>
            </td>
            <td style="padding: 1rem;">
                <div style="display: flex; gap: 0.5rem;">
                    <button class="action-btn btn-update" onclick="editInspectionRecord(${index})"> Update</button>
                    <button class="action-btn btn-delete" onclick="deleteInspectionRecord(${index})"> Delete</button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Render classification table
function renderClassificationTable() {
    const tbody = document.getElementById('classificationRecordsBody');
    if (!tbody) {
        console.log('Classification table body not found, skipping render...');
        return;
    }
    
    if (classificationRecords.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" style="padding: 2rem; text-align: center; color: #a1887f;">No classification records yet. Start classifying eggs to see records here.</td></tr>';
        return;
    }
    
    tbody.innerHTML = classificationRecords.map((record, index) => `
        <tr style="border-bottom: 1px solid #efebe9;">
            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${record.date}</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${record.time}</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${record.goodEggs} eggs</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${record.crackedEggs} eggs</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #000; font-weight: 700;">${record.goodEggs + record.crackedEggs} eggs</td>
            <td style="padding: 1rem;">
                <span style="display: inline-block; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; background: #e8f5e9; color: #2e7d32;">
                     Classified
                </span>
            </td>
            <td style="padding: 1rem;">
                <div style="display: flex; gap: 0.5rem;">
                    <button class="action-btn btn-update" onclick="editClassificationRecord(${index})"> Update</button>
                    <button class="action-btn btn-delete" onclick="deleteClassificationRecord(${index})"> Delete</button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Render sales table
function renderSalesTable() {
    const tbody = document.getElementById('salesRecordsBody');
    if (!tbody) {
        return;
    }
    
    if (salesRecords.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" style="padding: 2rem; text-align: center; color: #000;">No sales records yet. Start recording sales to see records here.</td></tr>';
        return;
    }
    
    const productNames = {
        'eggs': 'Quail Eggs',
        'live_quail': 'Live Quail',
        'dressed_quail': 'Dressed Quail'
    };
    
   tbody.innerHTML = salesRecords.map((record, index) => `
    <tr style="border-bottom: 1px solid #efebe9;">
        <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${record.date}</td>
        <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${record.time}</td>
        <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${productNames[record.productType] || record.productType}</td>
        <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${record.quantity} ${record.productType === 'eggs' ? 'trays' : 'pcs'}</td>
        <td style="padding: 1rem; font-size: 0.88rem; color: #000;">₱${record.price.toFixed(2)}</td>
        <td style="padding: 1rem; font-size: 0.88rem; color: #000; font-weight: 700;">₱${record.total.toFixed(2)}</td>
        <td style="padding: 1rem;">
            <span style="display: inline-block; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; background: #e8f5e9; color: #2e7d32;">
                Sold
            </span>
        </td>
        <td style="padding: 1rem;">
            <div style="display: flex; gap: 0.5rem;">
                <button class="action-btn btn-update" onclick="editSalesRecord(${index})">Update</button>
                <button class="action-btn btn-delete" onclick="deleteSalesRecord(${index})">Delete</button>
            </div>
        </td>
    </tr>
`).join('');
}

// Update Modal Functions
function closeUpdateModal() {
    document.getElementById('updateModal').classList.remove('show');
}

function saveUpdateModal() {
    const modal = document.getElementById('updateModal');
    const recordType = modal.dataset.recordType;
    const recordIndex = parseInt(modal.dataset.recordIndex);
    const recordId = modal.dataset.recordId;
    
    if (recordType === 'quail') {
        // Validate form before submitting
        const form = document.getElementById('quailManagementForm');
        
        // Get values to validate
        const liveQuails = document.getElementById('liveQuailsInput').value;
        const deadQuails = document.getElementById('deadQuailsInput').value;
        const quailDate = document.getElementById('quailCountDate').value;
        
        // Validate inputs
        if (!quailDate) {
            alert('Please select a date');
            return;
        }
        if (liveQuails === '' || isNaN(liveQuails) || liveQuails < 0) {
            alert('Please enter a valid number for live quails');
            return;
        }
        if (deadQuails === '' || isNaN(deadQuails) || deadQuails < 0) {
            alert('Please enter a valid number for dead quails');
            return;
        }
        
        // Close modal first
        closeUpdateModal();
        
        // Submit the quail management form after a brief delay to allow modal to close
        setTimeout(() => {
            form.submit();
        }, 200);
        return;
    }
    else if (recordType === 'collection-db') {
        const cage = document.getElementById('update-collection-db-cage').value;
        const eggs = parseInt(document.getElementById('update-collection-db-eggs').value);
        const goodEggs = parseInt(document.getElementById('update-collection-db-good').value) || 0;
        const crackedEggs = parseInt(document.getElementById('update-collection-db-cracked').value) || 0;
        const time = document.getElementById('update-collection-db-time').value;
        const notes = document.getElementById('update-collection-db-notes').value;
        
        // Validate that good + cracked = total
        if (goodEggs + crackedEggs !== eggs) {
            alert('Good eggs + Cracked eggs must equal Total eggs.');
            return;
        }
        
        // Update in database
        fetch(`/api/egg-collections/${recordId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                cage_pen: cage,
                total_eggs: eggs,
                good_eggs: goodEggs,
                cracked_eggs: crackedEggs,
                collection_time: time,
                notes: notes
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                closeUpdateModal();
                // Reload the page to refresh the table
                location.reload();
                addNotification('', `Updated collection record: ${eggs} eggs from Cage/Pen ${cage}`);
            } else {
                alert('Error updating collection: ' + (result.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating collection');
        });
        
        return;
    }
    else if (recordType === 'collection') {
        const cage = document.getElementById('update-collection-cage').value;
        const eggs = parseInt(document.getElementById('update-collection-eggs').value);
        const goodEggs = parseInt(document.getElementById('update-collection-good').value) || 0;
        const crackedEggs = parseInt(document.getElementById('update-collection-cracked').value) || 0;
        const eggSize = document.getElementById('update-collection-size').value;
        const feedBrand = document.getElementById('update-collection-feed').value;
        const time = document.getElementById('update-collection-time').value;
        
        collectionRecords[recordIndex] = {
            ...collectionRecords[recordIndex],
            cagePen: cage,
            eggs: eggs,
            goodEggs: goodEggs,
            crackedEggs: crackedEggs,
            eggSize: eggSize,
            feedBrand: feedBrand,
            time: time
        };
        
        saveCollectionRecords();
        renderCollectionTable();
        
        const sizeText = eggSize === 'mixed' ? 'Mixed Sizes' : 
                        eggSize === 'small' ? 'Small (15-18g)' :
                        eggSize === 'medium' ? 'Medium (18-22g)' : 'Large (22g+)';
        const feedBrandText = feedBrand === 'quail_layer_smash' ? 'Quail Layer Smash' : 'Jedstar Feeds';
        
        addNotification('', `Updated collection record: ${eggs} eggs (${goodEggs} good, ${crackedEggs} cracked) from Cage/Pen ${cage} - ${sizeText}, ${feedBrandText}`);
    }
    else if (recordType === 'production') {
        const date = document.getElementById('update-production-date').value;
        const eggs = parseInt(document.getElementById('update-production-eggs').value);
        
        productionRecords[recordIndex] = {
            ...productionRecords[recordIndex],
            date: new Date(date).toLocaleDateString(),
            totalEggs: eggs
        };
        
        saveProductionRecords();
        renderProductionTable();
        updateProductionCounters();
        addNotification('', `Updated production record: ${eggs} eggs`);
    }
    else if (recordType === 'inspection') {
        const total = parseInt(document.getElementById('update-inspection-total').value);
        const good = parseInt(document.getElementById('update-inspection-good').value);
        const cracked = parseInt(document.getElementById('update-inspection-cracked').value);
        
        inspectionRecords[recordIndex] = {
            ...inspectionRecords[recordIndex],
            totalInspected: total,
            goodEggs: good,
            crackedEggs: cracked
        };
        
        saveInspectionRecords();
        renderInspectionTable();
        updateInspectionCounters();
        addNotification('', `Updated inspection record: ${good} good, ${cracked} cracked`);
    }
    else if (recordType === 'classification') {
        const good = parseInt(document.getElementById('update-classification-good').value);
        const cracked = parseInt(document.getElementById('update-classification-cracked').value);
        
        classificationRecords[recordIndex] = {
            ...classificationRecords[recordIndex],
            goodEggs: good,
            crackedEggs: cracked
        };
        
        saveClassificationRecords();
        renderClassificationTable();
        updateClassificationCounters();
        addNotification('', `Updated classification record: ${good} good, ${cracked} cracked`);
    }
    else if (recordType === 'sales') {
        const product = document.getElementById('update-sales-product').value;
        const quantity = parseInt(document.getElementById('update-sales-quantity').value);
        const price = parseFloat(document.getElementById('update-sales-price').value);
        const total = quantity * price;
        
        salesRecords[recordIndex] = {
            ...salesRecords[recordIndex],
            productType: product,
            quantity: quantity,
            price: price,
            total: total
        };
        
        saveSalesRecords();
        renderSalesTable();
        updateSalesCounters();
        if (document.getElementById('analyticsFromDate')) {
            updateAnalytics();
        }
        const productNames = { 'eggs': 'Quail Eggs', 'live_quail': 'Live Quail', 'dressed_quail': 'Dressed Quail' };
        addNotification('', `Updated sales record: ${quantity} ${productNames[product]}`);
    }
    
    closeUpdateModal();
    
    // Auto-update analytics after any record update
    setTimeout(() => {
        if (typeof updateAnalyticsData === 'function') {
            updateAnalyticsData();
        }
        if (typeof updateRealAnalyticsData === 'function') {
            updateRealAnalyticsData();
        }
    }, 100);
}

// Refresh Collection Table from Database
function refreshCollectionTableFromDB() {
    fetch('/api/egg-collections', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success && result.collections) {
            const tbody = document.getElementById('collectionTableBody');
            if (!tbody) return;
            
            if (result.collections.length === 0) {
                tbody.innerHTML = '<tr><td colspan="10" style="padding: 2rem; text-align: center; color: #a1887f;">No collections recorded yet. Start collecting eggs to see records here.</td></tr>';
                return;
            }
            
            const eggSizeDisplay = {
                'mixed': 'Mixed Sizes',
                'small': 'Small (15-18g)',
                'medium': 'Medium (18-22g)',
                'large': 'Large (22g+)'
            };
            
            const feedBrandDisplay = {
                'quail_layer_smash': 'Quail Layer Smash',
                'jedstar': 'Jedstar Feeds'
            };
            
            tbody.innerHTML = result.collections.map(collection => {
                const createdAt = new Date(collection.created_at);
                const collectionTime = collection.collection_time ? 
                    new Date(`1970-01-01T${collection.collection_time}`).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }) :
                    createdAt.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                
                return `
                    <tr style="border-bottom: 1px solid #efebe9;">
                        <td style="padding: 1rem; font-size: 0.88rem; color: #6d4c41; font-weight: 600;">${createdAt.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</td>
                        <td style="padding: 1rem; font-size: 0.88rem; color: #8d6e63;">${collectionTime}</td>
                        <td style="padding: 1rem; font-size: 0.88rem; color: #6d4c41; text-align: center; font-weight: 600;">Cage/Pen ${collection.cage_pen}</td>
                        <td style="padding: 1rem; font-size: 0.88rem; color: #6d4c41; text-align: center; font-weight: 700;">${collection.total_eggs}</td>
                        <td style="padding: 1rem; font-size: 0.88rem; color: #4caf50; text-align: center; font-weight: 700;">${collection.good_eggs}</td>
                        <td style="padding: 1rem; font-size: 0.88rem; color: #d32f2f; text-align: center; font-weight: 700;">${collection.cracked_eggs}</td>
                        <td style="padding: 1rem; font-size: 0.88rem; color: #8d6e63; text-align: center;">${eggSizeDisplay[collection.egg_size] || 'Mixed'}</td>
                        <td style="padding: 1rem; font-size: 0.88rem; color: #8d6e63;">${feedBrandDisplay[collection.feed_brand] || 'Quail Layer Smash'}</td>
                        <td style="padding: 1rem; text-align: center;">
                            <span style="display: inline-block; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; background: #e8f5e9; color: #2e7d32;">Recorded</span>
                        </td>
                        <td style="padding: 1rem; text-align: center;">
                            <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                <button class="action-btn btn-update" onclick="editCollectionFromDB(${collection.id})" title="Edit" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">Edit</button>
                                <button class="action-btn btn-delete" onclick="deleteCollectionFromDB(${collection.id})" title="Delete" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">Delete</button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }
    })
    .catch(error => {
        console.error('Error refreshing collection table:', error);
    });
}

// Edit and Delete Functions for Collection Records from Database
function editCollectionFromDB(collectionId) {
    // Fetch the collection data from the database
    fetch(`/api/egg-collections/${collectionId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success && result.collection) {
            const record = result.collection;
            
            const modalContent = `
                <form id="update-collection-db-form">
                    <div class="update-form-group">
                        <label class="update-form-label">Cage/Pen:</label>
                        <select class="update-form-input" id="update-collection-db-cage" required>
                            <option value="1" ${record.cage_pen === '1' ? 'selected' : ''}>Cage/Pen 1</option>
                            <option value="2" ${record.cage_pen === '2' ? 'selected' : ''}>Cage/Pen 2</option>
                            <option value="3" ${record.cage_pen === '3' ? 'selected' : ''}>Cage/Pen 3</option>
                            <option value="4" ${record.cage_pen === '4' ? 'selected' : ''}>Cage/Pen 4</option>
                        </select>
                    </div>
                    <div class="update-form-group">
                        <label class="update-form-label">Total Eggs Collected:</label>
                        <input type="number" class="update-form-input" id="update-collection-db-eggs" value="${record.total_eggs}" required>
                    </div>
                    <div class="update-form-group">
                        <label class="update-form-label">Good Eggs:</label>
                        <input type="number" class="update-form-input" id="update-collection-db-good" value="${record.good_eggs}" required>
                    </div>
                    <div class="update-form-group">
                        <label class="update-form-label">Cracked Eggs:</label>
                        <input type="number" class="update-form-input" id="update-collection-db-cracked" value="${record.cracked_eggs}" required>
                    </div>
                    <div class="update-form-group">
                        <label class="update-form-label">Collection Time:</label>
                        <input type="time" class="update-form-input" id="update-collection-db-time" value="${record.collection_time || ''}" required>
                    </div>
                    <div class="update-form-group">
                        <label class="update-form-label">Notes:</label>
                        <textarea class="update-form-input" id="update-collection-db-notes" rows="3">${record.notes || ''}</textarea>
                    </div>
                </form>
            `;
            
            document.getElementById('updateModalContent').innerHTML = modalContent;
            document.getElementById('updateModal').classList.add('show');
            document.getElementById('updateModal').dataset.recordType = 'collection-db';
            document.getElementById('updateModal').dataset.recordId = collectionId;
        } else {
            alert('Error loading collection data: ' + (result.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error loading collection data');
    });
}

function deleteCollectionFromDB(collectionId) {
    showConfirmModal({
        icon: '',
        title: 'Delete Collection Record',
        message: 'Are you sure you want to delete this collection record? This action cannot be undone.',
        buttonText: 'Yes, Delete',
        onConfirm: function() {
            fetch(`/api/egg-collections/${collectionId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // Reload the page to refresh the table
                    location.reload();
                } else {
                    alert('Error deleting collection: ' + (result.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting collection');
            });
        }
    });
}
// Edit and Delete Functions for Collection Records
function editCollectionRecord(index) {
    const records = JSON.parse(localStorage.getItem('collectionRecords') || '[]');
    const record = records[index];
    
    const modalContent = `
        <form id="update-collection-form">
            <div class="update-form-group">
                <label class="update-form-label">Cage/Pen:</label>
                <select class="update-form-input" id="update-collection-cage" required>
                    <option value="1" ${record.cagePen === '1' ? 'selected' : ''}>Cage/Pen 1</option>
                    <option value="2" ${record.cagePen === '2' ? 'selected' : ''}>Cage/Pen 2</option>
                    <option value="3" ${record.cagePen === '3' ? 'selected' : ''}>Cage/Pen 3</option>
                    <option value="4" ${record.cagePen === '4' ? 'selected' : ''}>Cage/Pen 4</option>
                </select>
            </div>
            <div class="update-form-group">
                <label class="update-form-label">Total Eggs Collected:</label>
                <input type="number" class="update-form-input" id="update-collection-eggs" value="${record.eggs}" required>
            </div>
            <div class="update-form-group">
                <label class="update-form-label">Good Eggs:</label>
                <input type="number" class="update-form-input" id="update-collection-good" value="${record.goodEggs || 0}" required>
            </div>
            <div class="update-form-group">
                <label class="update-form-label">Cracked Eggs:</label>
                <input type="number" class="update-form-input" id="update-collection-cracked" value="${record.crackedEggs || 0}" required>
            </div>
            <div class="update-form-group">
                <label class="update-form-label">Egg Size:</label>
                <select class="update-form-input" id="update-collection-size" required>
                    <option value="mixed" ${(record.eggSize || 'mixed') === 'mixed' ? 'selected' : ''}>Mixed Sizes</option>
                    <option value="small" ${record.eggSize === 'small' ? 'selected' : ''}>Small (15-18g)</option>
                    <option value="medium" ${record.eggSize === 'medium' ? 'selected' : ''}>Medium (18-22g)</option>
                    <option value="large" ${record.eggSize === 'large' ? 'selected' : ''}>Large (22g+)</option>
                </select>
            </div>
            <div class="update-form-group">
                <label class="update-form-label">Feed Brand:</label>
                <select class="update-form-input" id="update-collection-feed" required>
                    <option value="quail_layer_smash" ${(record.feedBrand || 'quail_layer_smash') === 'quail_layer_smash' ? 'selected' : ''}>Quail Layer Smash</option>
                    <option value="jedstar" ${record.feedBrand === 'jedstar' ? 'selected' : ''}>Jedstar Feeds</option>
                </select>
            </div>
            <div class="update-form-group">
                <label class="update-form-label">Collection Time:</label>
                <input type="time" class="update-form-input" id="update-collection-time" value="${record.time}" required>
            </div>
        </form>
    `;
    
    document.getElementById('updateModalContent').innerHTML = modalContent;
    document.getElementById('updateModal').classList.add('show');
    document.getElementById('updateModal').dataset.recordType = 'collection';
    document.getElementById('updateModal').dataset.recordIndex = index;
}

function deleteCollectionRecord(index) {
    const record = collectionRecords[index];
    if (!record) return;
    
    showConfirmModal({
        icon: '',
        title: 'Delete Collection Record',
        message: `Delete collection record of ${record.eggs} eggs from Cage/Pen ${record.cagePen}?`,
        buttonText: 'Yes, Delete',
        onConfirm: function() {
            collectionRecords.splice(index, 1);
            saveCollectionRecords();
            renderCollectionTable();
            addNotification('', `Deleted collection record: ${record.eggs} eggs from Cage/Pen ${record.cagePen}`);
            updateAnalytics();
        }
    });
}

// Edit and Delete Functions for Production Records
function editProductionRecord(index) {
    const records = JSON.parse(localStorage.getItem('productionRecords') || '[]');
    const record = records[index];
    
    const modalContent = `
        <form id="update-production-form">
            <div class="update-form-group">
                <label class="update-form-label">Date:</label>
                <input type="date" class="update-form-input" id="update-production-date" value="${new Date(record.timestamp).toISOString().split('T')[0]}" required>
            </div>
            <div class="update-form-group">
                <label class="update-form-label">Total Eggs:</label>
                <input type="number" class="update-form-input" id="update-production-eggs" value="${record.totalEggs}" required>
            </div>
        </form>
    `;
    
    document.getElementById('updateModalContent').innerHTML = modalContent;
    document.getElementById('updateModal').classList.add('show');
    document.getElementById('updateModal').dataset.recordType = 'production';
    document.getElementById('updateModal').dataset.recordIndex = index;
}

function deleteProductionRecord(index) {
    const record = productionRecords[index];
    if (!record) return;
    
    showConfirmModal({
        title: 'Delete Production Record',
        message: `Delete production record of ${record.totalEggs} eggs?`,
        buttonText: 'Yes, Delete',
        onConfirm: function() {
            productionRecords.splice(index, 1);
            saveProductionRecords();
            renderProductionTable();
            updateProductionCounters();
            addNotification('', `Deleted production record: ${record.totalEggs} eggs`);
            updateAnalytics();
        }
    });
}

// Edit and Delete Functions for Inspection Records
function editInspectionRecord(index) {
    const records = JSON.parse(localStorage.getItem('inspectionRecords') || '[]');
    const record = records[index];
    
    const modalContent = `
        <form id="update-inspection-form">
            <div class="update-form-group">
                <label class="update-form-label">Total Inspected:</label>
                <input type="number" class="update-form-input" id="update-inspection-total" value="${record.totalInspected}" required>
            </div>
            <div class="update-form-group">
                <label class="update-form-label">Good Eggs:</label>
                <input type="number" class="update-form-input" id="update-inspection-good" value="${record.goodEggs}" required>
            </div>
            <div class="update-form-group">
                <label class="update-form-label">Cracked Eggs:</label>
                <input type="number" class="update-form-input" id="update-inspection-cracked" value="${record.crackedEggs}" required>
            </div>
        </form>
    `;
    
    document.getElementById('updateModalContent').innerHTML = modalContent;
    document.getElementById('updateModal').classList.add('show');
    document.getElementById('updateModal').dataset.recordType = 'inspection';
    document.getElementById('updateModal').dataset.recordIndex = index;
}

function deleteInspectionRecord(index) {
    const record = inspectionRecords[index];
    if (!record) return;
    
    showConfirmModal({
        title: 'Delete Inspection Record',
        message: `Delete inspection record of ${record.totalInspected} eggs?`,
        buttonText: 'Yes, Delete',
        onConfirm: function() {
            inspectionRecords.splice(index, 1);
            saveInspectionRecords();
            renderInspectionTable();
            updateInspectionCounters();
            addNotification('', `Deleted inspection record: ${record.totalInspected} eggs`);
            updateAnalytics();
        }
    });
}

// Edit and Delete Functions for Classification Records
function editClassificationRecord(index) {
    const records = JSON.parse(localStorage.getItem('classificationRecords') || '[]');
    const record = records[index];
    
    const modalContent = `
        <form id="update-classification-form">
            <div class="update-form-group">
                <label class="update-form-label">Good Eggs:</label>
                <input type="number" class="update-form-input" id="update-classification-good" value="${record.goodEggs}" required>
            </div>
            <div class="update-form-group">
                <label class="update-form-label">Cracked Eggs:</label>
                <input type="number" class="update-form-input" id="update-classification-cracked" value="${record.crackedEggs}" required>
            </div>
        </form>
    `;
    
    document.getElementById('updateModalContent').innerHTML = modalContent;
    document.getElementById('updateModal').classList.add('show');
    document.getElementById('updateModal').dataset.recordType = 'classification';
    document.getElementById('updateModal').dataset.recordIndex = index;
}

function deleteClassificationRecord(index) {
    const record = classificationRecords[index];
    if (!record) return;
    
    showConfirmModal({
        title: 'Delete Classification Record',
        message: `Delete classification record of ${record.goodEggs + record.crackedEggs} eggs?`,
        buttonText: 'Yes, Delete',
        onConfirm: function() {
            classificationRecords.splice(index, 1);
            saveClassificationRecords();
            renderClassificationTable();
            updateClassificationCounters();
            addNotification('', `Deleted classification record: ${record.goodEggs + record.crackedEggs} eggs`);
            updateAnalytics();
        }
    });
}

// Edit and Delete Functions for Sales Records
function editSalesRecord(index) {
    const records = JSON.parse(localStorage.getItem('salesRecords') || '[]');
    const record = records[index];
    
    const modalContent = `
        <form id="update-sales-form">
            <div class="update-form-group">
                <label class="update-form-label">Product Type:</label>
                <select class="update-form-input" id="update-sales-product" onchange="updateSalesPrice('update-sales-product', 'update-sales-price')" required>
                    <option value="eggs" ${record.productType === 'eggs' ? 'selected' : ''}>Quail Eggs</option>
                    <option value="live_quail" ${record.productType === 'live_quail' ? 'selected' : ''}>Live Quail</option>
                    <option value="dressed_quail" ${record.productType === 'dressed_quail' ? 'selected' : ''}>Dressed Quail</option>
                </select>
            </div>
            <div class="update-form-group">
                <label class="update-form-label">Quantity:</label>
                <input type="number" class="update-form-input" id="update-sales-quantity" value="${record.quantity}" required>
            </div>
            <div class="update-form-group">
                <label class="update-form-label">Price per Unit:</label>
                <input type="number" step="0.01" class="update-form-input" id="update-sales-price" value="${record.price}" required>
            </div>
        </form>
    `;
    
    document.getElementById('updateModalContent').innerHTML = modalContent;
        updateSalesPrice('update-sales-product', 'update-sales-price');
    document.getElementById('updateModal').classList.add('show');
    document.getElementById('updateModal').dataset.recordType = 'sales';
    document.getElementById('updateModal').dataset.recordIndex = index;
}

function deleteSalesRecord(index) {
    const record = salesRecords[index];
    if (!record) return;
    
    const productNames = { 'eggs': 'Quail Eggs', 'live_quail': 'Live Quail', 'dressed_quail': 'Dressed Quail' };
    const productName = productNames[record.productType] || record.productType;
    
    showConfirmModal({
        title: 'Delete Sales Record',
        message: `Delete sales record: ${record.quantity} ${productName} for ₱${Number(record.total).toFixed(2)}? This will also remove it from Analytics & Reports.`,
        buttonText: 'Yes, Delete',
        onConfirm: function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            // Refresh local UI + analytics after removal
            const finishLocal = function(stock) {
                salesRecords.splice(index, 1);
                saveSalesRecords();
                renderSalesTable();
                updateSalesCounters();

                // Stock was restored on the server when the sale was deleted
                if (stock) {
                    window.quailInventoryState = window.quailInventoryState || {};
                    window.quailInventoryState.live_quails = stock.live_quails;
                    window.quailInventoryState.dressed_quails_stock = stock.dressed_quails_stock;
                    updateQuailStockDisplay();
                }

                addNotification('', `Deleted sales record: ${record.quantity} ${productName}`);

                setTimeout(() => {
                    if (typeof updateRealAnalyticsData === 'function') {
                        updateRealAnalyticsData();
                    }
                }, 100);
            };

            if (record.id) {
                // Recorded in the database â€” delete it there too so
                // Analytics & Reports stop counting this sale.
                fetch('/api/sales/' + record.id, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(result => {
                    if (!result.success) {
                        throw new Error(result.message || 'Failed to delete sale from database');
                    }
                    finishLocal(result.stock);
                })
                .catch(error => {
                    console.error('Error deleting sale from database:', error);
                    alert('Error deleting sale from database: ' + error.message);
                });
            } else {
                // Legacy/local-only record that never reached the database
                finishLocal(null);
            }
        }
    });
}

// Render collection table
function renderCollectionTable() {
    const tbody = document.getElementById('collectionTableBody');
    if (!tbody) {
        console.log('Collection table body not found');
        return;
    }
    
    if (collectionRecords.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" style="padding: 2rem; text-align: center; color: #a1887f;">No collections recorded yet. Start collecting eggs to see records here.</td></tr>';
        return;
    }
    
    const eggSizeDisplay = {
        'mixed': 'Mixed Sizes',
        'small': 'Small (15-18g)',
        'medium': 'Medium (18-22g)',
        'large': 'Large (22g+)'
    };
    
    const feedBrandDisplay = {
        'quail_layer_smash': 'Quail Layer Smash',
        'jedstar': 'Jedstar Feeds'
    };
    
    tbody.innerHTML = collectionRecords.map((record, index) => `
        <tr style="border-bottom: 1px solid #efebe9;">
            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${record.date}</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${record.time}</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">Cage/Pen ${record.cagePen}</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${record.eggs} eggs</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${record.goodEggs || 0} eggs</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${record.crackedEggs || 0} eggs</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${eggSizeDisplay[record.eggSize] || 'Mixed Sizes'}</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">${feedBrandDisplay[record.feedBrand] || 'Quail Layer Smash'}</td>
            <td style="padding: 1rem;">
                <span style="display: inline-block; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; background: #e8f5e9; color: #2e7d32;">
                    Recorded
                </span>
            </td>
            <td style="padding: 1rem;">
                <div style="display: flex; gap: 0.5rem;">
                    <button class="action-btn btn-update" onclick="editCollectionRecord(${index})">Update</button>
                    <button class="action-btn btn-delete" onclick="deleteCollectionRecord(${index})">Delete</button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Record Collection & Inspection Confirmation
function confirmRecordCollection() {
    const cagePen = document.getElementById('collectCagePen').value;
    const totalEggs = parseInt(document.getElementById('collectEggs').value) || 0;
    const goodEggs = parseInt(document.getElementById('collectGoodEggs').value) || 0;
    const crackedEggs = parseInt(document.getElementById('collectCrackedEggs').value) || 0;
    const eggSize = document.getElementById('collectEggSize').value;
    const feedBrand = document.getElementById('collectFeedBrand').value;
    const time = document.getElementById('collectTime').value;
    
    if (totalEggs <= 0) {
        showConfirmModal({
            icon: '',
            title: 'Invalid Input',
            message: 'Please enter a valid number of total eggs.',
            buttonText: 'OK',
            onConfirm: function() {}
        });
        return;
    }
    
    if (goodEggs + crackedEggs !== totalEggs) {
        showConfirmModal({
            icon: '',
            title: 'Invalid Input',
            message: 'Good eggs + Cracked eggs must equal Total eggs collected.',
            buttonText: 'OK',
            onConfirm: function() {}
        });
        return;
    }
    
    const feedBrandName = feedBrand === 'quail_layer_smash' ? 'Quail Layer Smash' : 'Jedstar Feeds';
    const sizeText = eggSize === 'mixed' ? 'Mixed Sizes' : 
                     eggSize === 'small' ? 'Small (15-18g)' :
                     eggSize === 'medium' ? 'Medium (18-22g)' : 'Large (22g+)';
    
    showConfirmModal({
        icon: '',
        title: 'Record Collection & Inspection',
        message: `Save collection of ${totalEggs} eggs (${goodEggs} good, ${crackedEggs} cracked) from Cage/Pen ${cagePen}?\n\nSize: ${sizeText}\nFeed Brand: ${feedBrandName}`,
        buttonText: 'Yes, Save',
        onConfirm: function() {
            console.log('User confirmed, recording collection to database...');
            
            // Save to database via API
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            
            if (!csrfToken) {
                console.error('CSRF token not found');
                showConfirmModal({
                    icon: '??',
                    title: 'Error',
                    message: 'Security token not found. Please refresh the page and try again.',
                    buttonText: 'OK',
                    onConfirm: function() {}
                });
                return;
            }
            
            fetch('/api/egg-collections', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    cage_pen: cagePen,
                    total_eggs: totalEggs,
                    good_eggs: goodEggs,
                    cracked_eggs: crackedEggs,
                    egg_size: eggSize,
                    feed_brand: feedBrand,
                    collection_time: time
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    console.log('Collection saved to database:', result.collection);
                    
                    // Also add to localStorage for immediate display
                    const now = new Date();
                    const record = {
                        date: now.toLocaleDateString(),
                        time: time || now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }),
                        cagePen: cagePen,
                        eggs: totalEggs,
                        goodEggs: goodEggs,
                        crackedEggs: crackedEggs,
                        eggSize: eggSize,
                        feedBrand: feedBrand,
                        timestamp: now.getTime()
                    };
                    
                    collectionRecords.unshift(record); // Add to beginning of array
                    saveCollectionRecords();
                    renderCollectionTable();
                    
                    // Refresh the database table as well
                    refreshCollectionTableFromDB();
                    
                    // Update daily data
                    dailyData.totalCollected += totalEggs;
                    dailyData.goodEggs += goodEggs;
                    dailyData.crackedEggs += crackedEggs;
                    saveDailyData();
                    updateSummaryDisplay();
                    addActivity('', 'Collection & Inspection', `${totalEggs} eggs (${goodEggs} good, ${crackedEggs} cracked) from Cage/Pen ${cagePen} - ${sizeText}, ${feedBrandName}`);
                    addNotification('', `Collected & inspected ${totalEggs} eggs from Cage/Pen ${cagePen}`);
                    
                    // Update analytics immediately
                    setTimeout(() => {
                        if (typeof updateRealAnalyticsData === 'function') {
                            updateRealAnalyticsData();
                        }
                    }, 100);
                    
                    // Show success notification
                    setTimeout(() => {
                        showConfirmModal({
                            icon: '',
                            title: 'Collection Recorded!',
                            message: `${totalEggs} eggs collected and inspected from Cage/Pen ${cagePen} saved to database`,
                            buttonText: 'OK',
                            onConfirm: function() {}
                        });
                    }, 300);
                    
                    // Reset form
                    document.getElementById('collectEggs').value = '';
                    document.getElementById('collectGoodEggs').value = '';
                    document.getElementById('collectCrackedEggs').value = '';
                    document.getElementById('collectEggSize').value = 'mixed';
                    document.getElementById('collectFeedBrand').value = 'quail_layer_smash';
                    // Keep the current time for next collection
                    const newCurrentTime = new Date().toTimeString().slice(0, 5);
                    document.getElementById('collectTime').value = newCurrentTime;
                } else {
                    throw new Error(result.message || 'Failed to save collection');
                }
            })
            .catch(error => {
                console.error('Error saving collection:', error);
                showConfirmModal({
                    icon: '',
                    title: 'Error',
                    message: 'Failed to save collection to database: ' + error.message,
                    buttonText: 'OK',
                    onConfirm: function() {}
                });
            });
        }
    });
}

// Save Production Confirmation
function confirmSaveProduction() {
    const date = document.getElementById('productionDate').value;
    const eggs = parseInt(document.getElementById('productionEggs').value) || 0;
    
    if (eggs <= 0) {
        showConfirmModal({
            icon: '',
            title: 'Invalid Input',
            message: 'Please enter a valid number of eggs.',
            buttonText: 'OK',
            onConfirm: function() {
                closeConfirmModal();
            }
        });
        return;
    }
    
    showConfirmModal({
        icon: '',
        title: 'Save Production',
        message: `Record production of ${eggs} eggs?`,
        buttonText: 'Yes, Save',
        onConfirm: function() {
            // Calculate distribution across cages (simple distribution)
            const cage1 = Math.floor(eggs * 0.35);
            const cage2 = Math.floor(eggs * 0.35);
            const cage3 = eggs - cage1 - cage2;
            
            // Add to production records
            const now = new Date();
            const record = {
                date: date || now.toLocaleDateString(),
                time: now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }),
                cage1: cage1,
                cage2: cage2,
                cage3: cage3,
                totalEggs: eggs,
                timestamp: now.getTime()
            };
            
            productionRecords.unshift(record); // Add to beginning of array
            saveProductionRecords();
            renderProductionTable();
            updateProductionCounters();
            
            // Update daily data
            dailyData.totalCollected += eggs;
            saveDailyData();
            updateSummaryDisplay();
            addActivity('', 'Production Record', `${eggs} total eggs`);
            addNotification('', `Production recorded: ${eggs} eggs`);
            
            // Update analytics immediately
            if (document.getElementById('analytics')) {
                updateAnalytics();
            }
            
            // Close modal first, then show success
            closeConfirmModal();
            
            // Show success modal after a brief delay
            setTimeout(() => {
                showConfirmModal({
                    icon: '',
                    title: 'Production Saved!',
                    message: `${eggs} eggs production recorded`,
                    buttonText: 'OK',
                    onConfirm: function() {
                        closeConfirmModal();
                    }
                });
            }, 100);
            
            // Reset form
            document.getElementById('productionEggs').value = '';
        }
    });
}

// Complete Inspection Confirmation
function confirmCompleteInspection() {
    const total = parseInt(document.getElementById('inspectTotal').value) || 0;
    const good = parseInt(document.getElementById('inspectGood').value) || 0;
    const cracked = parseInt(document.getElementById('inspectCracked').value) || 0;
    
    if (total <= 0 || (good + cracked) <= 0) {
        showConfirmModal({
            icon: '',
            title: 'Invalid Input',
            message: 'Please enter valid inspection numbers.',
            buttonText: 'OK',
            onConfirm: function() {}
        });
        return;
    }
    
    showConfirmModal({
        icon: '',
        title: 'Complete Inspection',
        message: `Record inspection: ${good} good, ${cracked} cracked eggs?`,
        buttonText: 'Yes, Complete',
        onConfirm: function() {
            // Add to inspection records
            const now = new Date();
            const record = {
                date: now.toLocaleDateString(),
                time: now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }),
                totalInspected: total,
                goodEggs: good,
                crackedEggs: cracked,
                timestamp: now.getTime()
            };
            
            inspectionRecords.unshift(record); // Add to beginning of array
            saveInspectionRecords();
            renderInspectionTable();
            updateInspectionCounters();
            
            // Update daily data
            dailyData.goodEggs += good;
            dailyData.crackedEggs += cracked;
            saveDailyData();
            updateSummaryDisplay();
            addActivity('', 'Egg Inspection', ` ${good} good,  ${cracked} cracked`);
            addNotification('', `Inspection complete: ${good} good, ${cracked} cracked`);
            
            // Update analytics immediately
            if (document.getElementById('analytics')) {
                updateAnalytics();
            }
            
            showSuccessModal('', 'Inspection Complete!', `${good} good eggs, ${cracked} cracked eggs recorded`);
            
            // Reset form
            document.getElementById('inspectTotal').value = '';
            document.getElementById('inspectGood').value = '';
            document.getElementById('inspectCracked').value = '';
        }
    });
}

// Update Stock Confirmation
function confirmUpdateStock() {
    const good = parseInt(document.getElementById('classifyGood').value) || 0;
    const cracked = parseInt(document.getElementById('classifyCracked').value) || 0;
    
    if ((good + cracked) <= 0) {
        showConfirmModal({
            icon: '',
            title: 'Invalid Input',
            message: 'Please enter valid stock numbers.',
            buttonText: 'OK',
            onConfirm: function() {}
        });
        return;
    }
    
    showConfirmModal({
        icon: '',
        title: 'Update Stock',
        message: `Update stock: ${good} sellable, ${cracked} damaged?`,
        buttonText: 'Yes, Update',
        onConfirm: function() {
            // Add to classification records
            const now = new Date();
            const record = {
                date: now.toLocaleDateString(),
                time: now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }),
                goodEggs: good,
                crackedEggs: cracked,
                timestamp: now.getTime()
            };
            
            classificationRecords.unshift(record); // Add to beginning of array
            saveClassificationRecords();
            renderClassificationTable();
            updateClassificationCounters();
            
            // Update daily data
            dailyData.goodEggs = good;
            dailyData.crackedEggs = cracked;
            saveDailyData();
            updateSummaryDisplay();
            addActivity('', 'Stock Update', ` ${good} sellable,  ${cracked} damaged`);
            addNotification('', `Stock updated: ${good} sellable, ${cracked} damaged`);
            
            // Update analytics immediately
            if (document.getElementById('analytics')) {
                updateAnalytics();
            }
            
            showSuccessModal('', 'Stock Updated!', `${good} sellable eggs, ${cracked} damaged eggs`);
            
            // Reset form
            document.getElementById('classifyGood').value = '';
            document.getElementById('classifyCracked').value = '';
        }
    });
}

// Record Sale Confirmation
function confirmRecordSale() {
    console.log('=== SALES RECORDING DEBUG ===');
    
    const quantity = parseInt(document.getElementById('salesQuantity').value) || 0;
    console.log('Quantity:', quantity);
    
    if (quantity <= 0) {
        console.log('Invalid quantity, showing error modal');
        showConfirmModal({
            icon: '',
            title: 'Invalid Input',
            message: 'Please enter a valid quantity.',
            buttonText: 'OK',
            onConfirm: function() {}
        });
        return;
    }
    
    const productType = document.getElementById('salesProductType').value;
    const price = parseFloat(document.getElementById('salesPrice').value) || 0;
    const customerName = document.querySelector('input[placeholder="Customer name"]').value || '';
    const total = quantity * price;
    const productNames = { 'eggs': 'Quail Eggs', 'live_quail': 'Live Quail', 'dressed_quail': 'Dressed Quail' };
    
    console.log('Product Type:', productType);
    console.log('Price:', price);
    console.log('Total:', total);
    console.log('Customer:', customerName);
    
    showConfirmModal({
        icon: '',
        title: 'Record Sale',
        message: `Record sale of ${quantity} ${productNames[productType]} for ₱${total.toFixed(2)}?`,
        buttonText: 'Yes, Record',
        onConfirm: function() {
            console.log('User confirmed, recording sale to database...');

            const availableStock = getQuailStock(productType);
            if ((productType === 'live_quail' || productType === 'dressed_quail') && quantity > availableStock) {
                showConfirmModal({
                    icon: '',
                    title: 'Insufficient Stock',
                    message: `Only ${availableStock} ${productType === 'live_quail' ? 'live quail' : 'dressed quail'} left in stock.`,
                    buttonText: 'OK',
                    onConfirm: function() {}
                });
                return;
            }
            
            // Save to database via API
            fetch('/api/sales', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    product_type: productType,
                    quantity: quantity,
                    price: price,
                    customer_name: customerName
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    console.log('Sale saved to database:', result.sale);
                    
                    // Also add to localStorage for immediate display
                    const now = new Date();
                    const record = {
                        id: result.sale.id, // database ID â€” needed so deleting this record also removes it from Analytics & Reports
                        date: now.toLocaleDateString(),
                        time: now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }),
                        productType: productType,
                        quantity: quantity,
                        price: price,
                        total: total,
                        timestamp: now.getTime()
                    };
                    
                    console.log('New sale record:', record);
                    console.log('Current salesRecords array before adding:', salesRecords);
                    
                    salesRecords.unshift(record); // Add to beginning of array
                    console.log('salesRecords array after adding:', salesRecords.length, 'records');
                    
                    saveSalesRecords();
                    console.log('Sales records saved to localStorage');

                    if (result.stock) {
                        window.quailInventoryState = window.quailInventoryState || {};
                        window.quailInventoryState.live_quails = result.stock.live_quails;
                        window.quailInventoryState.dressed_quails_stock = result.stock.dressed_quails_stock;
                        updateQuailStockDisplay();
                    }
                    
                    renderSalesTable();
                    console.log('Sales table rendered');
                    
                    updateSalesCounters();
                    console.log('Sales counters updated');
                    if (document.getElementById('analyticsFromDate')) {
                        updateAnalytics();
                        console.log('Sales analytics updated');
                    }
                    
                    // Update daily data
                    if (productType === 'eggs') {
                        dailyData.eggsSold += quantity;
                        dailyData.eggsIncome += total;
                    } else if (productType === 'live_quail') {
                        dailyData.liveQuailSold += quantity;
                        dailyData.quailIncome += total;
                    } else if (productType === 'dressed_quail') {
                        dailyData.dressedQuailSold += quantity;
                        dailyData.quailIncome += total;
                    }
                    saveDailyData();
                    updateSummaryDisplay();
                    addActivity('', 'Sale Recorded', `${productNames[productType]} x${quantity} = ₱${total.toFixed(2)}`);
                    addNotification('', `Sale: ${quantity} ${productNames[productType]} = ₱${total.toFixed(2)}`);
                    
                    // Update analytics immediately
                    setTimeout(() => {
                        if (typeof updateRealAnalyticsData === 'function') {
                            updateRealAnalyticsData();
                        }
                    }, 100);
                    
                    // Show success notification
                    setTimeout(() => {
                        showConfirmModal({
                            icon: '',
                            title: 'Sale Recorded!',
                            message: `${productNames[productType]} x${quantity} = ₱${total.toFixed(2)} saved to database`,
                            buttonText: 'OK',
                            onConfirm: function() {}
                        });
                    }, 300);
                    
                    // Reset form
                    document.getElementById('salesQuantity').value = '';
                    document.querySelector('input[placeholder="Customer name"]').value = '';
                    calculateSalesTotal();
                    
                    console.log('=== SALES RECORDING COMPLETE ===');
                } else {
                    throw new Error(result.message || 'Failed to save sale');
                }
            })
            .catch(error => {
                console.error('Error saving sale:', error);
                showConfirmModal({
                    icon: '',
                    title: 'Error',
                    message: 'Failed to save sale to database: ' + error.message,
                    buttonText: 'OK',
                    onConfirm: function() {}
                });
            });
        }
    });
}

// Success Modal
function showSuccessModal(icon, title, message) {
    // This function is now replaced by the improved modal flow in individual functions
    showConfirmModal({
        icon: icon,
        title: title,
        message: message,
        buttonText: 'OK',
        onConfirm: function() {
            closeConfirmModal();
        }
    });
}

// Set up confirm button click
document.addEventListener('DOMContentLoaded', function() {
    renderNotifications();
    checkUrlForTab();
});

// Download Transactions as CSV
function downloadTransactions() {
    const table = document.getElementById('transactions-table');
    let csvContent = "data:text/csv;charset=utf-8,";
    
    // Add header
    csvContent += "Date,Type,Quantity,Price,Total,Status\n";
    
    // Get rows
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const cols = row.querySelectorAll('td');
        let rowData = [];
        cols.forEach(col => {
            rowData.push(col.textContent.trim());
        });
        csvContent += rowData.join(",") + "\n";
    });
    
    // Create download link
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "transactions_" + new Date().toISOString().slice(0,10) + ".csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Print Daily Summary
function printSummary() {
    const summaryCards = document.querySelectorAll('#summary .info-card');
    let summaryHTML = '<html><head><title>Daily Summary Report</title>';
    summaryHTML += '<style>';
    summaryHTML += 'body { font-family: Arial, sans-serif; padding: 20px; }';
    summaryHTML += 'h1, h2 { color: #6d4c41; text-align: center; }';
    summaryHTML += 'h2 { font-size: 18px; margin-top: 30px; border-bottom: 2px solid #6d4c41; padding-bottom: 10px; }';
    summaryHTML += '.card { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 8px; }';
    summaryHTML += '.card-icon { font-size: 24px; }';
    summaryHTML += '.card-title { font-weight: bold; color: #6d4c41; }';
    summaryHTML += '.card-text { font-size: 18px; color: #333; }';
    summaryHTML += '.activity { padding: 10px; border-bottom: 1px solid #eee; }';
    summaryHTML += '.activity-icon { font-size: 18px; margin-right: 10px; }';
    summaryHTML += '.activity-title { font-weight: bold; color: #4e342e; }';
    summaryHTML += '.activity-detail { color: #8d6e63; font-size: 14px; }';
    summaryHTML += '.activity-time { color: #a1887f; font-size: 12px; float: right; }';
    summaryHTML += '</style></head><body>';
    summaryHTML += '<h1>End-of-Day Summary Report</h1>';
    summaryHTML += '<p style="text-align: center;">Date: ' + new Date().toLocaleDateString() + '</p>';
    
    // Add Today's Activities
    summaryHTML += '<h2> Today\'s Activities</h2>';
    if (dailyData.activities.length > 0) {
        dailyData.activities.forEach(act => {
            summaryHTML += '<div class="activity">';
            summaryHTML += '<span class="activity-time">' + act.time + '</span>';
            summaryHTML += '<span class="activity-icon">' + act.icon + '</span>';
            summaryHTML += '<span class="activity-title">' + act.title + '</span><br>';
            summaryHTML += '<span class="activity-detail">' + act.detail + '</span>';
            summaryHTML += '</div>';
        });
    } else {
        summaryHTML += '<p style="color: #a1887f; text-align: center;">No activities recorded today.</p>';
    }
    
    // Add Summary Cards
    summaryHTML += '<h2> Summary</h2>';
    summaryCards.forEach(card => {
        const icon = card.querySelector('.info-card-icon').textContent;
        const title = card.querySelector('.info-card-title').textContent;
        const text = card.querySelector('.info-card-text').textContent;
        summaryHTML += '<div class="card">';
        summaryHTML += '<span class="card-icon">' + icon + '</span> ';
        summaryHTML += '<span class="card-title">' + title + ':</span> ';
        summaryHTML += '<span class="card-text">' + text + '</span>';
        summaryHTML += '</div>';
    });
    
    summaryHTML += '</body></html>';
    
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write(summaryHTML);
    printWindow.document.close();
    printWindow.print();
}

// Download Daily Summary as CSV
function downloadSummary() {
    const summaryCards = document.querySelectorAll('#summary .info-card');
    let csvContent = "data:text/csv;charset=utf-8,";
    
    // Add Activities Section
    csvContent += "Today's Activities\n";
    csvContent += "Time,Activity,Details\n";
    if (dailyData.activities.length > 0) {
        dailyData.activities.forEach(act => {
            csvContent += act.time + "," + act.title + "," + act.detail.replace(/,/g, ';') + "\n";
        });
    } else {
        csvContent += "No activities recorded\n";
    }
    
    csvContent += "\nSummary\n";
    csvContent += "Metric,Value\n";
    
    summaryCards.forEach(card => {
        const title = card.querySelector('.info-card-title').textContent;
        const text = card.querySelector('.info-card-text').textContent;
        csvContent += title + "," + text + "\n";
    });
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "daily_summary_" + new Date().toISOString().slice(0,10) + ".csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Quail Management Modal Functions
function openQuailUpdateModal() {
    const quailDate = document.getElementById('quailCountDate').value || new Date().toISOString().split('T')[0];
    const liveQuails = document.getElementById('liveQuailsInput').value || 0;
    const deadQuails = document.getElementById('deadQuailsInput').value || 0;
    const totalQuails = parseInt(liveQuails) + parseInt(deadQuails);
    const mortalityRate = totalQuails > 0 ? ((parseInt(deadQuails) / totalQuails) * 100).toFixed(2) : 0;
    
    // Format date to readable format
    const dateObj = new Date(quailDate + 'T00:00:00');
    const dateFormatted = dateObj.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    const dressedQuails = document.getElementById('dressedQuailsInput').value || 0;
    
    const modalContent = `
        <div style="text-align: center; padding: 0;">
            <p style="margin: 0 0 1.5rem 0; color: #6d4c41; font-size: 1rem; line-height: 1.5;">Are you sure you want to save this quail inventory update?</p>
            
            <div style="background: #f5f0eb; border-radius: 12px; padding: 1rem; margin-top: 1.5rem; text-align: left; border: 2px solid #d7ccc8;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <p style="margin: 0 0 0.5rem 0; color: #8d6e63; font-size: 0.85rem;"><strong>Date</strong></p>
                        <p style="margin: 0; color: #6d4c41; font-weight: 700; font-size: 0.95rem;">${dateFormatted}</p>
                    </div>
                    <div>
                        <p style="margin: 0 0 0.5rem 0; color: #8d6e63; font-size: 0.85rem;"><strong>Live Quails</strong></p>
                        <p style="margin: 0; color: #4caf50; font-weight: 700; font-size: 0.95rem;">${liveQuails}</p>
                    </div>
                    <div>
                        <p style="margin: 0 0 0.5rem 0; color: #8d6e63; font-size: 0.85rem;"><strong>Dead Quails</strong></p>
                        <p style="margin: 0; color: #d32f2f; font-weight: 700; font-size: 0.95rem;">${deadQuails}</p>
                    </div>
                    <div>
                        <p style="margin: 0 0 0.5rem 0; color: #8d6e63; font-size: 0.85rem;"><strong>Dressed Quails</strong></p>
                        <p style="margin: 0; color: #ff9800; font-weight: 700; font-size: 0.95rem;">${dressedQuails}</p>
                    </div>
                    <div>
                        <p style="margin: 0 0 0.5rem 0; color: #8d6e63; font-size: 0.85rem;"><strong>Mortality Rate</strong></p>
                        <p style="margin: 0; color: #ff9800; font-weight: 700; font-size: 0.95rem;">${mortalityRate}%</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('updateModalIcon').textContent = '';
    document.getElementById('updateModalTitle').textContent = 'Confirm Save';
    document.getElementById('updateModalContent').innerHTML = modalContent;
    document.getElementById('updateModal').classList.add('show');
    document.getElementById('updateModal').dataset.recordType = 'quail';
}
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize global chart registry to prevent "Canvas already in use" errors
window.squifmCharts = window.squifmCharts || {};

// Global chart registry to prevent "Canvas already in use" errors
window.squifmCharts = window.squifmCharts || {};


function renderNotifications() {
    if (window.__squifmBellFeedActive) return; // unified sidebar bell owns the notification list
    const list = document.getElementById('notification-list');
    const badge = document.getElementById('notification-badge');
    const notifications = getNotifications();
    if (!list || !badge) return;

    if (notifications.length === 0) {
        list.innerHTML = '<div class="notification-empty">No notifications yet.</div>';
        badge.style.display = 'none';
        badge.classList.remove('has-notifications');
        return;
    }

    const seenCount = getSeenCount();
    const unseenCount = Math.max(0, notifications.length - seenCount);
    
    if (unseenCount > 0) {
        badge.style.display = 'flex';
        badge.textContent = unseenCount;
        badge.classList.add('has-notifications');
    } else {
        badge.style.display = 'none';
        badge.classList.remove('has-notifications');
    }

    list.innerHTML = '';
    notifications.forEach(n => {
        const item = document.createElement('div');
        item.className = 'notification-item ' + (n.type || 'success');
        const isOrderNotif = n.message && n.message.includes('Order');
        item.style.cssText = 'display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.75rem; cursor: ' + (isOrderNotif ? 'pointer' : 'default') + ';';
        item.innerHTML = `
            <span class="notification-icon">${n.icon || (n.type === 'success' ? '' : '')}</span>
            <div class="notification-content">
                <div class="notification-text">${n.message}</div>
                <div class="notification-time">${n.time}</div>
            </div>
        `;
        if (isOrderNotif) {
            item.onclick = function() {
                goToOrdersTab();
                document.getElementById('notification-dropdown').classList.remove('show');
            };
        }
        list.appendChild(item);
    });
}

function toggleNotificationDropdown() {
    const dropdown = document.getElementById('notification-dropdown');
    const badge = document.getElementById('notification-badge');
    if (dropdown) {
        dropdown.classList.toggle('show');
        // Mark all notifications as seen when dropdown is opened
        if (dropdown.classList.contains('show')) {
            const notifications = getNotifications();
            setSeenCount(notifications.length);
            if (badge) {
                badge.textContent = '0';
                badge.style.display = 'none';
                badge.classList.remove('has-notifications');
            }
        }
    }
}

function clearNotifications(event) {
    event.stopPropagation();
    saveNotifications([]);
    renderNotifications();
}

document.addEventListener('click', function(e) {
    const bell = document.getElementById('notification-bell');
    const dropdown = document.getElementById('notification-dropdown');
    if (bell && dropdown && !bell.contains(e.target)) {
        dropdown.classList.remove('show');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    renderNotifications();
    checkUrlForTab();
});

// List all orders on the page
function listAllOrders() {
    console.log('=== LISTING ALL ORDERS ON PAGE ===');
    const orderRows = document.querySelectorAll('[data-order-id]');
    console.log('Found', orderRows.length, 'order rows');
    
    orderRows.forEach((row, index) => {
        const orderId = row.getAttribute('data-order-id');
        const badge = row.querySelector('[id^="status-badge-"]');
        const btn = row.querySelector('[id^="check-btn-"]');
        
        console.log(`Order ${index + 1}:`, {
            id: orderId,
            badgeText: badge ? badge.textContent.trim() : 'No badge',
            buttonDisabled: btn ? btn.disabled : 'No button',
            buttonId: btn ? btn.id : 'No button'
        });
    });
    
    if (orderRows.length === 0) {
        alert('No orders found on the page!');
    } else {
        alert(`Found ${orderRows.length} orders. Check console for details.`);
    }
}

// Debug the first order on the page
function debugFirstOrder() {
    const orderRows = document.querySelectorAll('[data-order-id]');
    if (orderRows.length === 0) {
        alert('No orders found on the page!');
        return;
    }
    
    const firstOrderId = orderRows[0].getAttribute('data-order-id');
    console.log('Debugging first order with ID:', firstOrderId);
    
    if (window.debugOrderStatus) {
        window.debugOrderStatus(firstOrderId);
    } else {
        console.error('debugOrderStatus function not found');
    }
}

// Test function for API
function testOrderStatusAPI() {
    console.log('=== TESTING ORDER STATUS API ===');
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    console.log('CSRF Token:', csrfToken ? 'Found' : 'Missing');
    
    if (!csrfToken) {
        alert('CSRF token missing! Make sure you are logged in.');
        return;
    }
    
    // Test the debug endpoint first
    fetch('/debug/orders', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => {
        console.log('Debug endpoint response status:', response.status);
        return response.text();
    })
    .then(text => {
        console.log('Debug endpoint response:', text);
        try {
            const data = JSON.parse(text);
            if (data.success && data.orders.length > 0) {
                const firstOrderId = data.orders[0].id;
                console.log('Testing with order ID:', firstOrderId);
                
                // Now test the actual status update
                return fetch('/orders/' + firstOrderId + '/status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: 'done' })
                });
            } else {
                throw new Error('No orders found or debug failed');
            }
        } catch (e) {
            throw new Error('Debug endpoint failed: ' + text);
        }
    })
    .then(response => {
        console.log('Status update response status:', response.status);
        return response.text();
    })
    .then(text => {
        console.log('Status update response:', text);
        try {
            const data = JSON.parse(text);
            if (data.success) {
                alert(' API Test Successful! Order status updated.');
            } else {
                alert(' API Test Failed: ' + (data.message || 'Unknown error'));
            }
        } catch (e) {
            alert(' API Test Failed: Invalid JSON response - ' + text);
        }
    })
    .catch(error => {
        console.error('API Test Error:', error);
        alert(' API Test Failed: ' + error.message);
    });
}

// Debug function to test order status update
window.debugOrderStatus = function(orderId) {
    console.log('=== DEBUG ORDER STATUS ===');
    console.log('Order ID:', orderId);
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    console.log('CSRF Token:', csrfToken ? 'Found' : 'Missing');
    
    const badge = document.getElementById('status-badge-' + orderId);
    console.log('Status badge element:', badge);
    console.log('Current badge text:', badge ? badge.textContent : 'Not found');
    
    const btn = document.getElementById('check-btn-' + orderId);
    console.log('Button element:', btn);
    console.log('Button disabled:', btn ? btn.disabled : 'Not found');
    
    // Test the API endpoint
    if (csrfToken) {
        console.log('Testing API endpoint...');
        fetch('/orders/' + orderId + '/status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: 'done' })
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.text();
        })
        .then(text => {
            console.log('Raw response:', text);
            try {
                const data = JSON.parse(text);
                console.log('Parsed response:', data);
            } catch (e) {
                console.log('Failed to parse JSON:', e);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
    }
    
    console.log('=== END DEBUG ===');
};

document.addEventListener('DOMContentLoaded', function() {
    renderNotifications();
    checkUrlForTab();
});
    @php
        $recentOrders = \App\Models\ContactMessage::where('subject', 'LIKE', 'ORDER:%')
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'desc')
            ->get();
    @endphp
    
    const notifiedOrders = JSON.parse(localStorage.getItem('notified_orders') || '[]');
    const newOrders = [
        @foreach($recentOrders as $order)
        @php
            $lines = explode("\n", $order->message);
            $orderData = [];
            foreach ($lines as $line) {
                $parts = explode(':', $line, 2);
                if (count($parts) == 2) {
                    $orderData[trim($parts[0])] = trim($parts[1]);
                }
            }
        @endphp
        {
            id: {{ $order->id }},
            name: "{{ $orderData['Name'] ?? $order->fullname }}",
            product: "{{ $orderData['Product'] ?? 'N/A' }}",
            qty: "{{ $orderData['Qty'] ?? 'N/A' }}",
            time: "{{ $order->created_at->format('M d, h:i A') }}"
        },
        @endforeach
    ];
    
    newOrders.forEach(order => {
        if (!notifiedOrders.includes(order.id)) {
            const productNames = {
                'quail_eggs': 'Quail Eggs',
                'live_quail': 'Live Quail', 
                'dressed_quail': 'Dressed Quail',
                'mixed': 'Mixed Order'
            };
            const productDisplay = productNames[order.product] || order.product;
            addNotification('', `New Order: ${order.name} ordered ${order.qty}x ${productDisplay}`, 'info', goToOrdersTab);
            notifiedOrders.push(order.id);
        }
    });
    
    localStorage.setItem('notified_orders', JSON.stringify(notifiedOrders));
}

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(checkNewOrders, 500);
    // Force load products immediately
    console.log('DOM loaded, loading products...');
    
    // Try to load products, but show defaults if it fails
    setTimeout(async function() {
        try {
            await loadProductsManagement();
        } catch (error) {
            console.log('Failed to load from API, showing default products');
            useDefaultProducts();
        }
    }, 1000);
    
    // Also show defaults immediately as fallback
    setTimeout(function() {
        const grid = document.getElementById('productsManagementGrid');
        if (grid && grid.innerHTML.includes('Loading products')) {
            console.log('Still loading after 3 seconds, showing defaults');
            useDefaultProducts();
        }
    }, 3000);
});

// Order Notifications
function checkNewOrders() {
function initializeAnalytics() {
    console.log('Initializing analytics...');
    
    // Set default date range (last 30 days)
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    
    const fromDateInput = document.getElementById('analyticsFromDate');
    const toDateInput = document.getElementById('analyticsToDate');
    
    if (fromDateInput) fromDateInput.value = thirtyDaysAgo.toISOString().split('T')[0];
    if (toDateInput) toDateInput.value = today.toISOString().split('T')[0];
    
    // Force update analytics with current data
    setTimeout(() => {
        console.log('Force updating analytics with current data...');
        updateAnalytics();
    }, 500);
}

// Debug function to test analytics manually
window.debugAnalytics = function() {
    console.log('=== ANALYTICS DEBUG ===');
    console.log('Current data arrays:');
    console.log('- collectionRecords:', collectionRecords);
    console.log('- salesRecords:', salesRecords);
    console.log('- productionRecords:', productionRecords);
    
    console.log('Analytics elements:');
    console.log('- analyticsEggsCollected:', document.getElementById('analyticsEggsCollected'));
    console.log('- analyticsTotalRevenue:', document.getElementById('analyticsTotalRevenue'));
    console.log('- analyticsFromDate:', document.getElementById('analyticsFromDate'));
    console.log('- analyticsToDate:', document.getElementById('analyticsToDate'));
    
    console.log('Calling updateAnalytics()...');
    updateAnalytics();
    console.log('=== DEBUG COMPLETE ===');
};

// Debug function to manually test localStorage
window.testLocalStorage = function() {
    console.log('=== TESTING LOCALSTORAGE ===');
    
    // Test saving
    const testData = [
        {
            date: new Date().toLocaleDateString(),
            time: new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }),
            productType: 'eggs',
            quantity: 5,
            price: 80,
            total: 400,
            timestamp: Date.now()
        }
    ];
    
    console.log('Saving test data:', testData);
    localStorage.setItem('salesRecords', JSON.stringify(testData));
    
    // Test loading
    const loaded = localStorage.getItem('salesRecords');
    console.log('Loaded from localStorage:', loaded);
    
    if (loaded) {
        const parsed = JSON.parse(loaded);
        console.log('Parsed data:', parsed);
        
        // Update the global array
        salesRecords = parsed;
        console.log('Updated salesRecords array:', salesRecords);
        
        // Render table
        renderSalesTable();
        updateSalesCounters();
        
        console.log('Table should now show the test data');
    }
    
    console.log('=== LOCALSTORAGE TEST COMPLETE ===');
};

// Debug function to check sales records
window.debugSalesRecords = function() {
    console.log('=== SALES RECORDS DEBUG ===');
    console.log('salesRecords array:', salesRecords);
    console.log('salesRecords length:', salesRecords.length);
    
    const stored = localStorage.getItem('salesRecords');
    console.log('localStorage salesRecords:', stored);
    
    if (stored) {
        const parsed = JSON.parse(stored);
        console.log('Parsed localStorage:', parsed);
        console.log('Parsed length:', parsed.length);
    }
    
    const tbody = document.getElementById('salesRecordsBody');
    console.log('Sales table body element:', tbody);
    console.log('Table body HTML:', tbody ? tbody.innerHTML : 'Not found');
    
    console.log('=== END DEBUG ===');
};

// Add this function to test sales data flow
window.testSalesFlow = function() {
    console.log('=== TESTING SALES DATA FLOW ===');
    
    // Check if salesRecords array exists and has data
    console.log('Current salesRecords array:', salesRecords);
    console.log('salesRecords length:', salesRecords.length);
    
    // Check localStorage
    const storedSales = localStorage.getItem('salesRecords');
    console.log('Stored sales in localStorage:', storedSales);
    
    if (storedSales) {
        const parsed = JSON.parse(storedSales);
        console.log('Parsed stored sales:', parsed);
        console.log('Parsed sales length:', parsed.length);
    }
    
    // Test adding a sale manually
    const testSale = {
        date: new Date().toLocaleDateString(),
        time: new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }),
        productType: 'eggs',
        quantity: 3,
        price: 80,
        total: 240,
        timestamp: Date.now()
    };
    
    console.log('Adding test sale:', testSale);
    salesRecords.unshift(testSale);
    saveSalesRecords();
    
    console.log('After adding test sale:');
    console.log('salesRecords length:', salesRecords.length);
    console.log('localStorage:', localStorage.getItem('salesRecords'));
    
    // Update analytics
    console.log('Updating analytics...');
    updateAnalytics();
    
    console.log('=== END TEST ===');
};

// Add test data function
function addTestData() {
    console.log('Adding test data...');
    
    // Add test collection record
    const testCollection = {
        date: new Date().toLocaleDateString(),
        time: new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }),
        cagePen: '1',
        eggs: 50,
        timestamp: Date.now()
    };
    collectionRecords.unshift(testCollection);
    saveCollectionRecords();
    
    // Add test sales record
    const testSale = {
        date: new Date().toLocaleDateString(),
        time: new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true }),
        productType: 'eggs',
        quantity: 5,
        price: 80,
        total: 400,
        timestamp: Date.now()
    };
    salesRecords.unshift(testSale);
    saveSalesRecords();
    
    console.log('Test data added. Updating analytics...');
    updateAnalytics();
}

// Chart instances
let bestSellingChartInstance = null;
let dailyProductionChartInstance = null;

// Initialize analytics when page loads
function initializeAnalytics() {
    console.log('Initializing analytics...');
    
    // Set default date range (last 30 days)
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    
    const fromDateInput = document.getElementById('analyticsFromDate');
    const toDateInput = document.getElementById('analyticsToDate');
    
    if (fromDateInput) fromDateInput.value = thirtyDaysAgo.toISOString().split('T')[0];
    if (toDateInput) toDateInput.value = today.toISOString().split('T')[0];
    
    // Force update analytics with current data
    setTimeout(() => {
        console.log('Force updating analytics with current data...');
        updateAnalytics();
    }, 500);
}

// Update Best Selling Products Chart
function updateBestSellingChart(eggsSold, liveQuailSold, dressedQuailSold) {
    const ctx = document.getElementById('bestSellingChart');
    if (!ctx) return;
    
    // Destroy existing chart
    if (bestSellingChartInstance) {
        bestSellingChartInstance.destroy();
    }
    
    bestSellingChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Quail Eggs', 'Live Quail', 'Dressed Quail'],
            datasets: [{
                data: [eggsSold, liveQuailSold, dressedQuailSold],
                backgroundColor: ['#a1887f', '#8d6e63', '#6d4c41'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const unit = label.includes('Eggs') ? ' trays' : ' pcs';
                            return label + ': ' + value + unit;
                        }
                    }
                }
            }
        }
    });
}

// Update Daily Production Chart
function updateDailyProductionChart(fromDate, toDate) {
    const ctx = document.getElementById('dailyProductionChart');
    if (!ctx) return;
    
    // Destroy existing chart
    if (dailyProductionChartInstance) {
        dailyProductionChartInstance.destroy();
    }
    
    // Get daily production data
    const dailyData = getDailyProductionData(fromDate, toDate);
    
    dailyProductionChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dailyData.labels,
            datasets: [{
                label: 'Eggs Produced',
                data: dailyData.values,
                borderColor: '#a1887f',
                backgroundColor: 'rgba(161, 136, 127, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#6d4c41',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#efebe9'
                    },
                    ticks: {
                        color: '#6d4c41'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6d4c41',
                        maxTicksLimit: 7
                    }
                }
            }
        }
    });
}

// Get daily production data for chart
function getDailyProductionData(fromDate, toDate) {
    const dailyTotals = {};
    
    // Process collection records
    collectionRecords.forEach(record => {
        const recordDate = new Date(record.timestamp);
        if (recordDate >= fromDate && recordDate <= toDate) {
            const dateStr = recordDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            dailyTotals[dateStr] = (dailyTotals[dateStr] || 0) + (record.eggs || 0);
        }
    });
    
    // Process production records
    productionRecords.forEach(record => {
        const recordDate = new Date(record.timestamp);
        if (recordDate >= fromDate && recordDate <= toDate) {
            const dateStr = recordDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            dailyTotals[dateStr] = (dailyTotals[dateStr] || 0) + (record.totalEggs || 0);
        }
    });
    
    // If no data, show sample data
    if (Object.keys(dailyTotals).length === 0) {
        return {
            labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
            values: [0, 0, 0, 0, 0, 0, 0]
        };
    }
    
    // Sort by date and return
    const sortedEntries = Object.entries(dailyTotals).sort((a, b) => {
        return new Date(a[0] + ', 2024') - new Date(b[0] + ', 2024');
    });
    
    return {
        labels: sortedEntries.map(entry => entry[0]),
        values: sortedEntries.map(entry => entry[1])
    };
}

function updateActivitiesSummary(fromDate, toDate) {
    const tableBody = document.getElementById('analyticsDataTableBody');
    if (!tableBody) {
        console.log('Analytics data table body not found');
        return;
    }
    
    // Get all records within date range
    let allRecords = [];
    

    
    // Add collection records
    const filteredCollection = collectionRecords.filter(record => {
        const recordDate = new Date(record.timestamp);
        return recordDate >= fromDate && recordDate <= toDate;
    });
    filteredCollection.forEach(record => {
        const recordDate = new Date(record.timestamp);
        allRecords.push({
            date: recordDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
            type: 'Collection',
            details: `Cage/Pen ${record.cagePen}`,
            quantity: `${record.eggs} eggs`,
            amount: '-',
            timestamp: record.timestamp
        });
    });
    
    // Add production records
    const filteredProduction = productionRecords.filter(record => {
        const recordDate = new Date(record.timestamp);
        return recordDate >= fromDate && recordDate <= toDate;
    });
    filteredProduction.forEach(record => {
        const recordDate = new Date(record.timestamp);
        allRecords.push({
            date: recordDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
            type: 'Production',
            details: `Total production recorded`,
            quantity: `${record.totalEggs} eggs`,
            amount: '-',
            timestamp: record.timestamp
        });
    });
    
    // Add inspection records
    const filteredInspection = inspectionRecords.filter(record => {
        const recordDate = new Date(record.timestamp);
        return recordDate >= fromDate && recordDate <= toDate;
    });
    filteredInspection.forEach(record => {
        const recordDate = new Date(record.timestamp);
        allRecords.push({
            date: recordDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
            type: 'Inspection',
            details: `${record.goodEggs} good, ${record.crackedEggs} cracked`,
            quantity: `${record.totalInspected} eggs`,
            amount: '-',
            timestamp: record.timestamp
        });
    });
    
    // Add classification records
    const filteredClassification = classificationRecords.filter(record => {
        const recordDate = new Date(record.timestamp);
        return recordDate >= fromDate && recordDate <= toDate;
    });
    filteredClassification.forEach(record => {
        const recordDate = new Date(record.timestamp);
        allRecords.push({
            date: recordDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
            type: 'Classification',
            details: `${record.goodEggs} sellable, ${record.crackedEggs} damaged`,
            quantity: `${record.goodEggs + record.crackedEggs} eggs`,
            amount: '-',
            timestamp: record.timestamp
        });
    });
    
    // Add sales records
    const filteredSales = salesRecords.filter(record => {
        const recordDate = new Date(record.timestamp);
        return recordDate >= fromDate && recordDate <= toDate;
    });
    const productNames = { 'eggs': 'Quail Eggs', 'live_quail': 'Live Quail', 'dressed_quail': 'Dressed Quail' };
    filteredSales.forEach(record => {
        const recordDate = new Date(record.timestamp);
        allRecords.push({
            date: recordDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
            type: 'Sales',
            details: productNames[record.productType] || record.productType,
            quantity: `${record.quantity} ${record.productType === 'eggs' ? 'trays' : 'pcs'}`,
            amount: `₱${record.total.toFixed(2)}`,
            timestamp: record.timestamp
        });
    });
    
    // Sort records by timestamp (summary first, then newest first)
    allRecords.sort((a, b) => b.timestamp - a.timestamp);
    
    console.log('Analytics table records found:', allRecords.length);
    
    if (allRecords.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="5" style="padding: 2rem; text-align: center; color: #a1887f;">No data available for the selected date range.</td></tr>';
        return;
    }
    
    // Populate table with all records
    tableBody.innerHTML = allRecords.map(record => {
        const isSummary = record.date === 'SUMMARY';
        const rowStyle = isSummary ? 'background: #f0f8ff; border: 2px solid #4CAF50; font-weight: 600;' : 'border-bottom: 1px solid #efebe9;';
        const textColor = isSummary ? '#2e7d32' : '#000';
        
        return `
        <tr style="${rowStyle}">
            <td style="padding: 1rem; font-size: 0.88rem; color: ${textColor};">${record.date}</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: ${textColor}; font-weight: 600;">${record.type}</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: ${textColor};">${record.details}</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: ${textColor};">${record.quantity}</td>
            <td style="padding: 1rem; font-size: 0.88rem; color: ${textColor}; font-weight: 600;">${record.amount}</td>
        </tr>
    `;
    }).join('');
}

// Export Functions
function exportAnalyticsPDF() {
    // Generate PDF using browser's print to PDF functionality
    const printContent = generateAnalyticsReport();
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Analytics Report - SQUIFM</title>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; padding: 20px; margin: 0; }');
    printWindow.document.write('h1, h2 { color: #6d4c41; page-break-after: avoid; }');
    printWindow.document.write('h1 { text-align: center; border-bottom: 2px solid #6d4c41; padding-bottom: 10px; }');
    printWindow.document.write('.metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; page-break-inside: avoid; }');
    printWindow.document.write('.metric { padding: 15px; border: 2px solid #d7ccc8; border-radius: 8px; text-align: center; background: #f9f9f9; }');
    printWindow.document.write('.metric-value { font-size: 24px; font-weight: bold; color: #2e7d32; margin-bottom: 5px; }');
    printWindow.document.write('.metric-label { font-size: 14px; color: #666; }');
    printWindow.document.write('@media print { body { margin: 0; } .no-print { display: none; } }');
    printWindow.document.write('</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(printContent);
    printWindow.document.write('<div class="no-print" style="text-align: center; margin-top: 30px;">');
    printWindow.document.write('<p style="color: #666; font-size: 14px;">To save as PDF: Press Ctrl+P (Windows) or Cmd+P (Mac), then select "Save as PDF" as destination.</p>');
    printWindow.document.write('</div>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    
    // Show instruction modal
    showConfirmModal({
        icon: '',
        title: 'Export to PDF',
        message: 'A new window opened. Press Ctrl+P (Windows) or Cmd+P (Mac), then select "Save as PDF" as destination to download the PDF file.',
        buttonText: 'Got it!',
        onConfirm: function() {}
    });
}

function exportAnalyticsExcel() {
    const table = document.getElementById('analyticsDataTable');
    if (!table) {
        alert('No analytics data table found!');
        return;
    }
    
    let csvContent = "data:text/csv;charset=utf-8,";
    
    // Add headers
    csvContent += "SQUIFM Analytics Report\n";
    csvContent += "Date Range: " + document.getElementById('analyticsFromDate').value + " to " + document.getElementById('analyticsToDate').value + "\n";
    csvContent += "Generated: " + new Date().toLocaleDateString() + "\n\n";
    
    // Add summary metrics
    csvContent += "SUMMARY METRICS\n";
    csvContent += "Total Eggs Collected," + document.getElementById('analyticsEggsCollected').textContent + "\n";
    csvContent += "Total Revenue," + document.getElementById('analyticsTotalRevenue').textContent + "\n";
    csvContent += "Best Selling Product," + document.getElementById('analyticsBestProduct').textContent + "\n";
    csvContent += "Total Production," + document.getElementById('analyticsProductionTotal').textContent + "\n";
    csvContent += "Daily Average," + document.getElementById('analyticsProductionAvg').textContent + "\n";
    csvContent += "Good Eggs," + document.getElementById('analyticsGoodEggs').textContent + "\n";
    csvContent += "Cracked Eggs," + document.getElementById('analyticsCrackedEggs').textContent + "\n";
    csvContent += "Eggs Sold," + document.getElementById('analyticsEggsSold').textContent + "\n";
    csvContent += "Live Quail Sold," + document.getElementById('analyticsLiveQuailSold').textContent + "\n";
    csvContent += "Dressed Quail Sold," + document.getElementById('analyticsDressedQuailSold').textContent + "\n\n";
    
    // Add detailed data table
    csvContent += "DETAILED ACTIVITY DATA\n";
    
    // Get table headers
    const headers = table.querySelectorAll('thead th');
    const headerRow = Array.from(headers).map(th => th.textContent.trim()).join(',');
    csvContent += headerRow + "\n";
    
    // Get table rows
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const cols = row.querySelectorAll('td');
        if (cols.length > 1) { // Skip empty state rows
            const rowData = Array.from(cols).map(col => {
                return '"' + col.textContent.trim().replace(/"/g, '""') + '"';
            });
            csvContent += rowData.join(',') + "\n";
        }
    });
    
    // Create download link
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "squifm_analytics_" + new Date().toISOString().slice(0,10) + ".csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Show success notification
    addNotification('', 'Analytics data exported to Excel/CSV successfully!');
}

function printAnalytics() {
    const printContent = generateAnalyticsReport();
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Print Analytics Report</title>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; padding: 20px; }');
    printWindow.document.write('h1, h2 { color: #6d4c41; text-align: center; }');
    printWindow.document.write('.metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0; }');
    printWindow.document.write('.metric { padding: 15px; border: 1px solid #ddd; border-radius: 8px; text-align: center; }');
    printWindow.document.write('.metric-value { font-size: 24px; font-weight: bold; color: #2e7d32; }');
    printWindow.document.write('.metric-label { font-size: 14px; color: #666; margin-top: 5px; }');
    printWindow.document.write('</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(printContent);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print(); // This one actually prints
}

function generateAnalyticsReport() {
    const fromDate = document.getElementById('analyticsFromDate').value;
    const toDate = document.getElementById('analyticsToDate').value;
    const table = document.getElementById('analyticsDataTable');
    
    let tableHTML = '';
    if (table) {
        // Get table HTML and style it for print
        const tableClone = table.cloneNode(true);
        tableHTML = `
            <h2> Complete Activity Data</h2>
            <div style="overflow-x: auto; margin: 20px 0;">
                <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
                    <thead>
                        <tr style="background: #f5f5f5;">
                            <th style="padding: 12px; text-align: left; border: 1px solid #ddd; font-weight: bold;">Date</th>
                            <th style="padding: 12px; text-align: left; border: 1px solid #ddd; font-weight: bold;">Activity Type</th>
                            <th style="padding: 12px; text-align: left; border: 1px solid #ddd; font-weight: bold;">Details</th>
                            <th style="padding: 12px; text-align: left; border: 1px solid #ddd; font-weight: bold;">Quantity</th>
                            <th style="padding: 12px; text-align: left; border: 1px solid #ddd; font-weight: bold;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const cols = row.querySelectorAll('td');
            if (cols.length > 1) { // Skip empty state rows
                tableHTML += '<tr>';
                cols.forEach(col => {
                    tableHTML += `<td style="padding: 10px; border: 1px solid #ddd;">${col.textContent.trim()}</td>`;
                });
                tableHTML += '</tr>';
            }
        });
        
        tableHTML += '</tbody></table></div>';
    }
    
    return `
        <h1> SQUIFM Analytics Report</h1>
        <p style="text-align: center;">Date Range: ${fromDate} to ${toDate}</p>
        <p style="text-align: center;">Generated: ${new Date().toLocaleDateString()}</p>
        
        <h2> Overview Metrics</h2>
        <div class="metrics-grid">
            <div class="metric">
                <div class="metric-value">${document.getElementById('analyticsEggsCollected').textContent}</div>
                <div class="metric-label">Total Eggs Collected</div>
            </div>
            <div class="metric">
                <div class="metric-value">${document.getElementById('analyticsTotalRevenue').textContent}</div>
                <div class="metric-label">Total Revenue</div>
            </div>
            <div class="metric">
                <div class="metric-value">${document.getElementById('analyticsActiveDays').textContent}</div>
                <div class="metric-label">Active Days</div>
            </div>
        </div>
        
        <h2> Production Analytics</h2>
        <div class="metrics-grid">
            <div class="metric">
                <div class="metric-value">${document.getElementById('analyticsProductionTotal').textContent}</div>
                <div class="metric-label">Total Eggs Produced</div>
            </div>
            <div class="metric">
                <div class="metric-value">${document.getElementById('analyticsProductionAvg').textContent}</div>
                <div class="metric-label">Daily Average</div>
            </div>
            <div class="metric">
                <div class="metric-value">${document.getElementById('analyticsGoodEggs').textContent}</div>
                <div class="metric-label">Good Eggs</div>
            </div>
            <div class="metric">
                <div class="metric-value">${document.getElementById('analyticsCrackedEggs').textContent}</div>
                <div class="metric-label">Cracked Eggs</div>
            </div>
        </div>
        
        <h2> Sales Analytics</h2>
        <div class="metrics-grid">
            <div class="metric">
                <div class="metric-value">${document.getElementById('analyticsTotalSales').textContent}</div>
                <div class="metric-label">Total Sales</div>
            </div>
            <div class="metric">
                <div class="metric-value">${document.getElementById('analyticsEggsSold').textContent}</div>
                <div class="metric-label">Eggs Sold (trays)</div>
            </div>
            <div class="metric">
                <div class="metric-value">${document.getElementById('analyticsLiveQuailSold').textContent}</div>
                <div class="metric-label">Live Quail Sold</div>
            </div>
            <div class="metric">
                <div class="metric-value">${document.getElementById('analyticsDressedQuailSold').textContent}</div>
                <div class="metric-label">Dressed Quail Sold</div>
            </div>
        </div>
        
        ${tableHTML}
    `;
}

function generateAnalyticsData() {
    return {
        overview: {
            eggsCollected: document.getElementById('analyticsEggsCollected').textContent,
            totalRevenue: document.getElementById('analyticsTotalRevenue').textContent,
            bestProduct: document.getElementById('analyticsBestProduct').textContent,
            activeDays: document.getElementById('analyticsActiveDays').textContent
        },
        production: {
            total: document.getElementById('analyticsProductionTotal').textContent,
            dailyAvg: document.getElementById('analyticsProductionAvg').textContent,
            goodEggs: document.getElementById('analyticsGoodEggs').textContent,
            crackedEggs: document.getElementById('analyticsCrackedEggs').textContent
        },
        sales: {
            totalSales: document.getElementById('analyticsTotalSales').textContent,
            eggsSold: document.getElementById('analyticsEggsSold').textContent,
            liveQuailSold: document.getElementById('analyticsLiveQuailSold').textContent,
            dressedQuailSold: document.getElementById('analyticsDressedQuailSold').textContent
        }
function updateNewOrderStatus(selectElement, id) {
    const newStatus = selectElement.value;
    const badge = document.getElementById(`order-status-badge-${id}`);
    
    // Store original color in case of error
    const originalBg = badge.style.background;
    const originalColor = badge.style.color;
    const originalText = badge.textContent;
    const originalIndex = selectElement.selectedIndex;
    
    badge.textContent = 'Updating...';
    badge.style.background = '#e2e8f0';
    badge.style.color = '#475569';
    
    fetch(`/admin/order/${id}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update badge text and color based on new status
            switch(newStatus) {
                case 'pending':
                    badge.style.background = '#fff3cd'; badge.style.color = '#856404';
                    badge.textContent = 'Pending';
                    break;
                case 'confirmed':
                    badge.style.background = '#cfe2ff'; badge.style.color = '#084298';
                    badge.textContent = 'Confirmed';
                    break;
                case 'completed':
                    badge.style.background = '#d1e7dd'; badge.style.color = '#0f5132';
                    badge.textContent = 'Completed';
                    break;
                case 'cancelled':
                    badge.style.background = '#f8d7da'; badge.style.color = '#842029';
                    badge.textContent = 'Cancelled';
                    break;
                default:
                    badge.style.background = '#e2e8f0'; badge.style.color = '#475569';
                    badge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
            }
        } else {
            alert('Error updating status: ' + data.message);
            // Revert changes
            badge.style.background = originalBg;
            badge.style.color = originalColor;
            badge.textContent = originalText;
            selectElement.selectedIndex = originalIndex;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the order status.');
        // Revert changes
        badge.style.background = originalBg;
        badge.style.color = originalColor;
        badge.textContent = originalText;
        selectElement.selectedIndex = originalIndex;
    });
}
</script>

<!-- Remove obsolete Analytics Update button and success popup -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    function removeObsoleteAnalyticsElements() {

        // Remove "Update Analytics" button only
        document.querySelectorAll('button').forEach(function (button) {
            if (button.textContent.trim().toLowerCase() === 'update analytics') {
                button.remove();
            }
        });

        // Remove obsolete Analytics success popup only
        document.querySelectorAll('*').forEach(function (element) {
            if (
                element.textContent &&
                element.textContent.trim() ===
                'The latest analytics data has been successfully updated.'
            ) {
                element.remove();
            }
        });
    }

    // Run after page loads
    removeObsoleteAnalyticsElements();

    // Detect elements added dynamically
    const observer = new MutationObserver(function () {
        removeObsoleteAnalyticsElements();
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});
</script>

<!-- Export Report Modal -->
<div class="confirm-modal-overlay" id="exportReportModal">
    <div class="confirm-modal" style="max-width: 450px; width: 90%;">
        <div class="confirm-modal-title" id="exportModalTitle">Export Report</div>

        <div id="exportModalContent" style="margin: 1.5rem 0; text-align: left;">
            <!-- Dynamic content will be inserted here -->
        </div>

        <div class="confirm-modal-buttons">
            <button class="confirm-modal-btn cancel" onclick="closeExportModal()">Cancel</button>
            <button class="confirm-modal-btn confirm" id="exportConfirmBtn" onclick="">Confirm</button>
        </div>
    </div>
</div>

<!-- Order Notification System - Orders Only -->
<script src="{{ asset('order-notifications.js') }}"></script>

@endsection
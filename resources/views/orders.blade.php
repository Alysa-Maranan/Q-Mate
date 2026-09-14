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
</style>

@include('components.navbar')

<div id="main-content-wrap" class="sidebar-content-wrap">
<div class="inventory-container">
    <div class="page-header">
        <h1 class="page-title">🛒 Orders & Products Management</h1>
    </div>

    <div class="section-card">
        <h2 class="section-title">Customer Orders & Product Management</h2>
        <p style="color: #8d6e63; margin-bottom: 1.5rem;">Manage customer orders and update product prices & stock levels.</p>
        
        @if(isset($customerOrders) && $customerOrders->count() > 0)
        <div style="background: #fff3cd; border-radius: 16px; padding: 1.5rem; margin-bottom: 2rem; border: 2px solid #ffc107;">
            <h3 style="color: #6d4c41; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-size: 1.5rem;">🛒</span> New Customer Orders ({{ $customerOrders->count() }})
            </h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0; background: white; border-radius: 12px; overflow: hidden;">
                    <thead>
                        <tr style="background: #f5f5f5;">
                            <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Date</th>
                            <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Customer</th>
                            <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Contact</th>
                            <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Address</th>
                            <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Product</th>
                            <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Qty</th>
                            <th style="padding: 1rem; text-align: left; color: #000; font-weight: 700; font-size: 0.85rem;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customerOrders as $order)
                        <tr style="border-bottom: 1px solid #efebe9;">
                            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">
                                {{ $order->created_at->format('M d, Y') }}<br>
                                <small style="color:#8d6e63;">{{ $order->created_at->format('h:i A') }}</small>
                            </td>
                            <td style="padding: 1rem; font-size: 0.88rem; color: #000; font-weight: 600;">{{ $order->name }}</td>
                            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">{{ $order->phone }}</td>
                            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">{{ $order->address }}</td>
                            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">{{ ucwords(str_replace('_', ' ', $order->product)) }}</td>
                            <td style="padding: 1rem; font-size: 0.88rem; color: #000;">{{ $order->quantity }}</td>
                            <td style="padding: 1rem;">
                                <span style="display: inline-block; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; 
                                    @if($order->status == 'pending') background: #fff3cd; color: #856404;
                                    @elseif($order->status == 'confirmed') background: #cfe2ff; color: #084298;
                                    @elseif($order->status == 'completed') background: #d1e7dd; color: #0f5132;
                                    @else background: #f8d7da; color: #842029; @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div style="background: #f5f0eb; border-radius: 16px; padding: 1.5rem; margin-bottom: 2rem; border: 2px solid #d7ccc8;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="color: #6d4c41; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 1.5rem;">📦</span> Product Management
                </h3>
                <div style="display: flex; gap: 0.5rem;">
                    <button onclick="useDefaultProducts()" style="padding: 0.5rem 1rem; background: #ff9800; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 0.85rem;">
                        Show Products
                    </button>
                    <button onclick="loadProductsManagement()" style="padding: 0.5rem 1rem; background: #6d4c41; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 0.85rem;">
                        Refresh
                    </button>
                </div>
            </div>
            <div id="productsManagementGrid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
                <div style="text-align: center; padding: 2rem; color: #8d6e63; border: 2px dashed #d7ccc8; border-radius: 12px;">
                    <div>Loading products...</div>
                    <div style="font-size: 0.8rem; margin-top: 0.5rem; opacity: 0.7;">If products don't load, click "Show Products" button</div>
                </div>
            </div>
        </div>
        

    </div>
</div>
</div>

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

<script src="{{ asset('js/inventory-orders.js') }}"></script>
@endsection

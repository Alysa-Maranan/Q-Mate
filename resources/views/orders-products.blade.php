@extends('layouts.app')

@section('content')
@include('components.sidebar')

<style>
    body {
        background: linear-gradient(135deg, #f5f0eb 0%, #efe8e4 50%, #e8dcd6 100%);
        min-height: 100vh;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    
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
    }

    .section-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(161,136,127,0.12);
        border: 1px solid rgba(161,136,127,0.1);
    }
    
    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #6d4c41;
        margin-bottom: 1rem;
    }
    
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
    .tab-badge {
        background: #ffa500;
        color: white;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.15rem 0.5rem;
        border-radius: 10px;
        margin-left: 0.25rem;
    }
    .category-tab.active .tab-badge {
        background: white;
        color: #a1887f;
    }
    .tab-badge-cancelled {
        background: #ff424f;
    }
    .category-tab.active .tab-badge-cancelled {
        background: white;
        color: #ff424f;
    }
</style>

<div id="main-content-wrap" class="sidebar-content-wrap">
<div class="container" style="padding: 2rem;">
    
    <!-- Header -->
    <div style="background: white; border-radius: 24px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 8px 32px rgba(161, 136, 127, 0.12); border: 1px solid rgba(161, 136, 127, 0.1); display: flex; flex-direction: column; align-items: center; text-align: center;">
        <h1 style="font-size: 2rem; font-weight: 800; color: #6d4c41; margin: 0;">Orders & Products</h1>
        <p style="margin: 0.5rem 0 0 0; color: #8d6e63; font-size: 1rem;">Manage customer orders and product inventory</p>
    </div>

    <!-- Product Update Confirmation Modal -->
    <div class="confirm-modal-overlay" id="productSuccessModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2100; justify-content: center; align-items: center;">
        <div class="confirm-modal" style="max-width: 400px; width: 90%; background: white; border-radius: 16px; padding: 2rem; box-shadow: 0 20px 40px rgba(0,0,0,0.2); position: relative; animation: slideUp 0.3s ease-out;">
            <div class="confirm-modal-title" style="font-size: 1.2rem; font-weight: 700; color: #6d4c41; margin-bottom: 1.5rem;">Success</div>
            <div id="productSuccessModalContent" style="margin-bottom: 2rem; color: #4e342e; font-size: 1rem;">Product updated successfully!</div>
            <div class="confirm-modal-buttons" style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button class="confirm-modal-btn confirm" onclick="closeProductSuccessModal()" style="padding: 0.5rem 1.2rem; background: #6d4c41; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">OK</button>
            </div>
        </div>
    </div>
    <div class="confirm-modal-overlay" id="productUpdateModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; justify-content: center; align-items: center;">
        <div class="confirm-modal" style="max-width: 400px; width: 90%; background: white; border-radius: 16px; padding: 2rem; box-shadow: 0 20px 40px rgba(0,0,0,0.2); position: relative; animation: slideUp 0.3s ease-out;">
            <div class="confirm-modal-title" id="productUpdateModalTitle" style="font-size: 1.2rem; font-weight: 700; color: #6d4c41; margin-bottom: 1.5rem;">Update Product</div>
            <div id="productUpdateModalContent" style="margin-bottom: 2rem; color: #4e342e; font-size: 1rem;">Are you sure you want to update this product's price and stock?</div>
            <div class="confirm-modal-buttons" style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button class="confirm-modal-btn cancel" onclick="closeProductUpdateModal()" style="padding: 0.5rem 1.2rem; background: #e0e0e0; color: #6d4c41; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">Cancel</button>
                <button class="confirm-modal-btn confirm" id="productUpdateModalYes" style="padding: 0.5rem 1.2rem; background: #6d4c41; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">Yes, Update</button>
            </div>
        </div>
    </div>

    <!-- Tabs Header -->
    <div class="category-tabs">
        <button onclick="switchTab('orders')" id="tabBtnOrders" class="category-tab active">
            <span class="category-icon"></span>Customer Orders
            <span class="tab-badge" id="ordersBadge">{{ isset($customerOrders) ? $customerOrders->where('status', '!=', 'cancelled')->count() : 0 }}</span>
        </button>
        <button onclick="switchTab('cancelled')" id="tabBtnCancelled" class="category-tab">
            <span class="category-icon"></span>Cancellations
            <span class="tab-badge tab-badge-cancelled" id="cancelledBadge">{{ isset($customerOrders) ? $customerOrders->where('status', 'cancelled')->count() : 0 }}</span>
        </button>
        <button onclick="switchTab('products')" id="tabBtnProducts" class="category-tab">
            <span class="category-icon"></span>Product Management
        </button>
        <button onclick="switchTab('reviews')" id="tabBtnReviews" class="category-tab">
            <span class="category-icon"></span>Product Reviews
            <span class="tab-badge" id="reviewsBadge">0</span>
        </button>
    </div>

    <!-- Customer Orders Section -->
    <div id="ordersSection" class="section-card">
        @php
            $activeOrders = isset($customerOrders) ? $customerOrders->where('status', '!=', 'cancelled') : collect();
            $cancelledOrders = isset($customerOrders) ? $customerOrders->where('status', 'cancelled') : collect();
        @endphp
        <h2 class="section-title">Customer Orders</h2>
        <p style="color: #8d6e63; margin-bottom: 1.5rem;">View and manage all active customer orders</p>
        
        @if($activeOrders->count() > 0)
            @foreach($activeOrders as $order)
            <div style="background: white; border-radius: 4px; border: 1px solid #e0e0e0; margin-bottom: 1rem; box-shadow: 0 1px 2px rgba(0,0,0,0.03);" data-order-id="{{ $order->id }}">
                <!-- Header -->
                <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid #f5f5f5; display: flex; justify-content: space-between; align-items: center; background: #fafafa;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem;">
                        <span style="font-weight: 600; color: #333;">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                        <span style="color: #ccc;">|</span>
                        <span style="color: #888;"><i class="ph-bold ph-clock" style="margin-right: 0.25rem;"></i>{{ $order->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="order-status-badge" style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase;
                        @if($order->status == 'pending') color: #ee4d2d;
                        @elseif($order->status == 'confirmed') color: #2196f3;
                        @elseif($order->status == 'to_ship') color: #9c27b0;
                        @elseif($order->status == 'completed') color: #26aa99;
                        @else color: #ff424f; @endif">
                        {{ $order->status == 'to_ship' ? 'To Ship' : ucfirst($order->status) }}
                    </div>
                </div>
                
                <!-- Body -->
                <div style="padding: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 60px; height: 60px; background: #f9f9f9; border: 1px solid #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #ccc; overflow: hidden;">
                            <img src="{{ $order->product_image }}" alt="{{ $order->product }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="flex: 1;">
                            <div style="font-size: 1rem; color: #333; margin-bottom: 0.25rem; font-weight: 500;">Product: {{ ucwords(str_replace('_', ' ', trim($order->product))) }}</div>
                            <div style="font-size: 0.85rem; color: #757575;">Quantity: {{ $order->quantity }}</div>
                            <div style="font-size: 0.85rem; color: #757575;">Customer: {{ $order->name }} | Contact: {{ $order->phone }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer / Actions -->
                <div style="padding: 1rem 1.25rem; border-top: 1px solid #f5f5f5; display: flex; justify-content: space-between; align-items: center; background: #fffcfb;">
                    <div style="font-size: 0.9rem; color: #333;">
                        Total: <span style="font-weight: 600; font-size: 1.2rem; color: #6d4c41;">{{ $order->formatted_total }}</span>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <button onclick="showOrderDetailsModal(this)" data-order="{{ json_encode([ 'id' => str_pad($order->id, 6, '0', STR_PAD_LEFT), 'name' => $order->name, 'phone' => $order->phone, 'address' => $order->address ?? 'Not provided', 'product' => ucwords(str_replace('_', ' ', $order->product)), 'quantity' => $order->quantity, 'total' => $order->formatted_total, 'status' => ($order->status == 'to_ship' ? 'To Ship' : ucfirst($order->status)), 'date' => $order->created_at->format('M d, Y h:i A') ]) }}" style="padding: 0.4rem 1.2rem; border: 1px solid #ccc; color: #555; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 0.85rem; background: white; transition: all 0.2s; cursor: pointer;" onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background='white'">View Order</button>
                    </div>
                </div>
            </div>
            @endforeach
        @else
        <div style="text-align: center; padding: 3rem; color: #8d6e63; background: #f5f0eb; border-radius: 12px; border: 2px dashed #d7ccc8;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">📦</div>
            <h3 style="color: #6d4c41; margin-bottom: 0.5rem;">No Active Orders</h3>
            <p>Active customer orders will appear here</p>
        </div>
        @endif
    </div>

    <!-- Cancelled Orders Section -->
    <div id="cancelledSection" class="section-card" style="display: none;">
        <h2 class="section-title">Cancellation Requests / Cancelled</h2>
        <p style="color: #8d6e63; margin-bottom: 1.5rem;">Review orders that are marked as cancelled</p>

        @if($cancelledOrders->count() > 0)
            @foreach($cancelledOrders as $order)
            <div style="background: white; border-radius: 4px; border: 1px solid #e0e0e0; margin-bottom: 1rem; box-shadow: 0 1px 2px rgba(0,0,0,0.03);" data-order-id="{{ $order->id }}">
                <!-- Header -->
                <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid #f5f5f5; display: flex; justify-content: space-between; align-items: center; background: #fafafa;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem;">
                        <span style="font-weight: 600; color: #333;">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                        <span style="color: #ccc;">|</span>
                        <span style="color: #888;"><i class="ph-bold ph-clock" style="margin-right: 0.25rem;"></i>{{ $order->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="order-status-badge" style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; color: #ff424f;">
                        {{ ucfirst($order->status) }}
                    </div>
                </div>

                <!-- Body -->
                <div style="padding: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 60px; height: 60px; background: #f9f9f9; border: 1px solid #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #ccc; overflow: hidden;">
                            <img src="{{ $order->product_image }}" alt="{{ $order->product }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="flex: 1;">
                            <div style="font-size: 1rem; color: #333; margin-bottom: 0.25rem; font-weight: 500;">Product: {{ ucwords(str_replace('_', ' ', trim($order->product))) }}</div>
                            <div style="font-size: 0.85rem; color: #757575;">Quantity: {{ $order->quantity }}</div>
                        </div>
                    </div>
                </div>

                <!-- Footer / Actions -->
                <div style="padding: 1rem 1.25rem; border-top: 1px solid #f5f5f5; display: flex; justify-content: space-between; align-items: center; background: #fffcfb;">
                    <div style="font-size: 0.9rem; color: #333;">
                        Total: <span style="font-weight: 600; font-size: 1.2rem; color: #6d4c41;">{{ $order->formatted_total }}</span>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <button onclick="showOrderDetailsModal(this)" data-order="{{ json_encode([ 'id' => str_pad($order->id, 6, '0', STR_PAD_LEFT), 'name' => $order->name, 'phone' => $order->phone, 'address' => $order->address ?? 'Not provided', 'product' => ucwords(str_replace('_', ' ', $order->product)), 'quantity' => $order->quantity, 'total' => $order->formatted_total, 'status' => ucfirst($order->status), 'date' => $order->created_at->format('M d, Y h:i A'), 'cancellation_reason' => $order->cancellation_reason ?? '' ]) }}" style="padding: 0.4rem 1.2rem; border: 1px solid #ccc; color: #555; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 0.85rem; background: white; transition: all 0.2s; cursor: pointer;" onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background='white'">Review Order</button>
                    </div>
                </div>
            </div>
            @endforeach
        @else
        <div style="text-align: center; padding: 3rem; color: #8d6e63; background: #f5f0eb; border-radius: 12px; border: 2px dashed #d7ccc8;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">📦</div>
            <h3 style="color: #6d4c41; margin-bottom: 0.5rem;">No Cancellations</h3>
            <p>There are no cancelled orders to review</p>
        </div>
        @endif
    </div>

    <!-- Product Management Section -->
    <div id="productsSection" class="section-card" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h2 class="section-title" style="margin: 0;">Product Management</h2>
                <p style="color: #8d6e63; margin: 0.5rem 0 0 0;">Update product prices and stock levels</p>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button onclick="useDefaultProducts()" style="padding: 0.5rem 1rem; background: #6d4c41; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 0.85rem; font-weight: 600; transition: background 0.2s;" onmouseover="this.style.background='#5d4037'" onmouseout="this.style.background='#6d4c41'">Show Products</button>
                <button onclick="loadProductsManagement()" style="padding: 0.5rem 1rem; background: #6d4c41; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 0.85rem; font-weight: 600; transition: background 0.2s;" onmouseover="this.style.background='#5d4037'" onmouseout="this.style.background='#6d4c41'">Refresh</button>
            </div>
        </div>
        <div id="productsManagementGrid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
            <div style="text-align: center; padding: 2rem; color: #8d6e63; border: 2px dashed #d7ccc8; border-radius: 12px;">
                <div>Loading products...</div>
                <div style="font-size: 0.8rem; margin-top: 0.5rem; opacity: 0.7;">If products don't load, click "Show Products" button</div>
            </div>
        </div>
    </div>

    <!-- Product Reviews Section -->
    <div id="reviewsSection" class="section-card" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h2 class="section-title">Product Reviews</h2>
                <p style="color: #8d6e63; margin: 0.5rem 0 0 0;">Manage and respond to customer product reviews</p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
            <div style="background: #f8f5f1; padding: 1rem; border-radius: 12px; text-align: center;">
                <div style="font-size: 1.5rem; font-weight: 700; color: #4e342e;" id="totalReviewsCount">-</div>
                <div style="font-size: 0.8rem; color: #8d6e63;">Total Reviews</div>
            </div>
            <div style="background: #fff3e0; padding: 1rem; border-radius: 12px; text-align: center;">
                <div style="font-size: 1.5rem; font-weight: 700; color: #e65100;" id="pendingReviewsCount">-</div>
                <div style="font-size: 0.8rem; color: #8d6e63;">Pending Reply</div>
            </div>
            <div style="background: #e8f5e9; padding: 1rem; border-radius: 12px; text-align: center;">
                <div style="font-size: 1.5rem; font-weight: 700; color: #2e7d32;" id="repliedReviewsCount">-</div>
                <div style="font-size: 0.8rem; color: #8d6e63;">Replied</div>
            </div>
            <div style="background: #fff8e1; padding: 1rem; border-radius: 12px; text-align: center;">
                <div style="font-size: 1.5rem; font-weight: 700; color: #ffc107;" id="avgRatingDisplay">-</div>
                <div style="font-size: 0.8rem; color: #8d6e63;">Avg Rating</div>
            </div>
        </div>

        <div class="category-tabs" style="margin-bottom: 1rem;">
            <button class="category-tab active" onclick="filterAdminReviews('all')" id="filterAllBtn">All</button>
            <button class="category-tab" onclick="filterAdminReviews('pending')" id="filterPendingBtn">Pending</button>
            <button class="category-tab" onclick="filterAdminReviews('replied')" id="filterRepliedBtn">Replied</button>
        </div>

        <div id="adminReviewsList" style="max-height: 500px; overflow-y: auto;">
            <div style="text-align: center; padding: 2rem; color: #8d6e63;">Loading reviews...</div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div id="orderDetailsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: flex-start; backdrop-filter: blur(4px); overflow-y: auto; padding: 2rem 0;">
        <div style="background: white; border-radius: 16px; padding: 2rem; width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 40px rgba(0,0,0,0.2); position: relative; animation: slideUp 0.3s ease-out; margin: 0 auto 2rem auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f5f0eb; padding-bottom: 1rem; margin-bottom: 1.5rem;">
                <h2 style="margin: 0; color: #6d4c41; font-size: 1.5rem;">Order Details</h2>
                <button onclick="closeOrderDetailsModal()" style="background: none; border: none; font-size: 1.5rem; color: #a1887f; cursor: pointer; padding: 0;">&times;</button>
            </div>
            
            <div style="display: grid; gap: 1rem;">
                <div style="background: #fdfbf9; padding: 1rem; border-radius: 8px; border: 1px solid #f5f0eb;">
                    <h3 style="margin: 0 0 0.5rem 0; color: #8d6e63; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Customer Information</h3>
                    <div style="display: grid; grid-template-columns: 100px 1fr; gap: 0.5rem; font-size: 0.95rem;">
                        <span style="color: #888;">Name:</span> <strong id="modalCustomerName" style="color: #333;"></strong>
                        <span style="color: #888;">Phone:</span> <span id="modalCustomerPhone" style="color: #333;"></span>
                        <span style="color: #888;">Address:</span> <span id="modalCustomerAddress" style="color: #333;"></span>
                    </div>
                </div>

                <div style="background: #fdfbf9; padding: 1rem; border-radius: 8px; border: 1px solid #f5f0eb;">
                    <h3 style="margin: 0 0 0.5rem 0; color: #8d6e63; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Order Information</h3>
                    <div style="display: grid; grid-template-columns: 100px 1fr; gap: 0.5rem; font-size: 0.95rem; align-items: center;">
                        <span style="color: #888;">Order ID:</span> <span id="modalOrderId" style="font-weight: 600; color: #333;"></span>
                        <span style="color: #888;">Date:</span> <span id="modalOrderDate" style="color: #333;"></span>

                        <span style="color: #888;">Status:</span>
                        <!-- For non-cancelled orders -->
                        <div id="statusEditableSection" style="display: flex; gap: 0.5rem; align-items: center;">
                            <select id="modalStatusSelect" style="padding: 0.3rem 0.5rem; border-radius: 4px; border: 1px solid #ccc; font-weight: 600; font-size: 0.85rem;">
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="to_ship">To Ship</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            <button onclick="updateOrderStatusFromModal()" style="padding: 0.3rem 0.8rem; background: #6d4c41; color: white; border: none; border-radius: 4px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#5d4037'" onmouseout="this.style.background='#6d4c41'">Save</button>
                        </div>
                        <!-- For cancelled orders -->
                        <div id="statusReadonlySection" style="display: none;">
                            <span id="modalStatusDisplay" style="padding: 0.3rem 0.8rem; border-radius: 4px; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; background: #ffcdd2; color: #c62828;">Cancelled</span>
                        </div>

                        <span style="color: #888;">Product:</span> <span id="modalOrderProduct" style="color: #333;"></span>
                        <span style="color: #888;">Quantity:</span> <span id="modalOrderQuantity" style="color: #333;"></span>
                        <span style="color: #888; margin-top: 0.5rem;">Total:</span> <strong id="modalOrderTotal" style="color: #6d4c41; font-size: 1.2rem; margin-top: 0.5rem;"></strong>
                    </div>
                </div>

                <div id="cancellationReasonSection" style="background: #fff8f8; padding: 1rem; border-radius: 8px; border: 1px solid #ffcdd2; margin-top: 1rem; display: none;">
                    <h3 style="margin: 0 0 0.5rem 0; color: #c62828; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Cancellation Reason</h3>
                    <div style="font-size: 0.95rem; color: #333;">
                        <strong id="modalCancellationReason"></strong>
                    </div>
                </div>
            </div>

            <div style="margin-top: 2rem; text-align: right;">
                <button onclick="closeOrderDetailsModal()" style="padding: 0.6rem 1.5rem; background: #6d4c41; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#5d4037'" onmouseout="this.style.background='#6d4c41'">Close</button>
            </div>
        </div>
    </div>

    <style>
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

</div>
</div>

<script>
// Order Details Modal Functions
let currentOrderIdForModal = null;

function showOrderDetailsModal(btn) {
    const order = JSON.parse(btn.getAttribute('data-order'));
    currentOrderIdForModal = order.id.replace('#', '').replace(/^0+/, ''); // remove padding zeroes
    
    document.getElementById('modalCustomerName').textContent = order.name;
    document.getElementById('modalCustomerPhone').textContent = order.phone;
    document.getElementById('modalCustomerAddress').textContent = order.address;
    
    document.getElementById('modalOrderId').textContent = '#' + order.id;
    document.getElementById('modalOrderDate').textContent = order.date;
    document.getElementById('modalOrderProduct').textContent = order.product;
    document.getElementById('modalOrderQuantity').textContent = order.quantity;
    document.getElementById('modalOrderTotal').textContent = order.total;
    
    const statusSelect = document.getElementById('modalStatusSelect');
    const statusLower = order.status.toLowerCase();
    
    for (let i = 0; i < statusSelect.options.length; i++) {
        if (statusSelect.options[i].value === statusLower) {
            statusSelect.selectedIndex = i;
            break;
        }
    }
    
    updateSelectColor(statusSelect);
    
    statusSelect.addEventListener('change', function() {
        updateSelectColor(this);
    });

    const modal = document.getElementById('orderDetailsModal');
    modal.style.display = 'flex';

    // Show/hide status sections based on order status
    const statusEditableSection = document.getElementById('statusEditableSection');
    const statusReadonlySection = document.getElementById('statusReadonlySection');
    const cancelReasonSection = document.getElementById('cancellationReasonSection');
    const cancelReasonEl = document.getElementById('modalCancellationReason');

    if (order.status.toLowerCase() === 'cancelled') {
        // Show read-only status for cancelled orders
        statusEditableSection.style.display = 'none';
        statusReadonlySection.style.display = 'flex';
        if (order.cancellation_reason) {
            cancelReasonSection.style.display = 'block';
            cancelReasonEl.textContent = order.cancellation_reason;
        } else {
            cancelReasonSection.style.display = 'none';
        }
    } else {
        // Show editable status for non-cancelled orders
        statusEditableSection.style.display = 'flex';
        statusReadonlySection.style.display = 'none';
        cancelReasonSection.style.display = 'none';
    }
}

function updateSelectColor(selectElement) {
    const val = selectElement.value;
    selectElement.style.color = '#6d4c41'; // Force brown color for the status select in modal
}

function updateOrderStatusFromModal() {
    if (!currentOrderIdForModal) return;
    
    const newStatus = document.getElementById('modalStatusSelect').value;
    
    fetch('/admin/order/' + currentOrderIdForModal + '/status', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: newStatus })
    }).then(response => {
        if(response.ok) {
            window.location.reload();
        } else {
            return response.json().then(data => {
                alert('Failed to update status: ' + (data.message || 'Unknown error'));
            }).catch(() => {
                alert('Failed to update status.');
            });
        }
    }).catch(error => {
        console.error('Error updating status:', error);
        alert('An error occurred.');
    });
}

function closeOrderDetailsModal() {
    document.getElementById('orderDetailsModal').style.display = 'none';
}

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('orderDetailsModal');
    if (event.target === modal) {
        closeOrderDetailsModal();
    }
});

// Tab Switching logic
function switchTab(tab) {
    const ordersSec = document.getElementById('ordersSection');
    const cancelledSec = document.getElementById('cancelledSection');
    const productsSec = document.getElementById('productsSection');
    const reviewsSec = document.getElementById('reviewsSection');

    const ordersBtn = document.getElementById('tabBtnOrders');
    const cancelledBtn = document.getElementById('tabBtnCancelled');
    const productsBtn = document.getElementById('tabBtnProducts');
    const reviewsBtn = document.getElementById('tabBtnReviews');

    // Hide all sections
    ordersSec.style.display = 'none';
    cancelledSec.style.display = 'none';
    productsSec.style.display = 'none';
    reviewsSec.style.display = 'none';

    // Remove active class from all tabs
    ordersBtn.classList.remove('active');
    cancelledBtn.classList.remove('active');
    productsBtn.classList.remove('active');
    reviewsBtn.classList.remove('active');

    if (tab === 'orders') {
        ordersSec.style.display = 'block';
        ordersBtn.classList.add('active');
    } else if (tab === 'cancelled') {
        cancelledSec.style.display = 'block';
        cancelledBtn.classList.add('active');
    } else if (tab === 'products') {
        productsSec.style.display = 'block';
        productsBtn.classList.add('active');

        // Auto-load products if switching to products tab and grid is still loading
        const grid = document.getElementById('productsManagementGrid');
        if (grid.innerHTML.includes('Loading products...')) {
            loadProductsManagement();
        }
    } else if (tab === 'reviews') {
        reviewsSec.style.display = 'block';
        reviewsBtn.classList.add('active');
        loadAdminReviews();
    }
}

// Product Management Functions
function loadProductsManagement() {
    fetch('/api/products')
        .then(response => response.json())
        .then(products => {
            displayProductsManagement(products);
        })
        .catch(error => {
            console.error('Error loading products:', error);
            useDefaultProducts();
        });
}

function useDefaultProducts() {
    const defaultProducts = [
        { id: 1, name: 'Fresh Quail Eggs', slug: 'quail_eggs', unit: 'tray (24 pieces)', price: 160, stock: 50 },
        { id: 2, name: 'Live Quail', slug: 'live_quail', unit: 'piece', price: 180, stock: 25 },
        { id: 3, name: 'Dressed Quail', slug: 'dressed_quail', unit: 'piece (cleaned)', price: 250, stock: 15 }
    ];
    displayProductsManagement(defaultProducts);
}

function displayProductsManagement(products) {
    const grid = document.getElementById('productsManagementGrid');
    if (!products || products.length === 0) {
        grid.innerHTML = '<div style="text-align: center; padding: 2rem; color: #8d6e63;">No products found</div>';
        return;
    }
    
    grid.innerHTML = products.map(product => `
        <div style="background: #f5f0eb; border-radius: 12px; padding: 1.5rem; border: 2px solid #d7ccc8;">
            <h3 style="color: #6d4c41; margin: 0 0 0.5rem 0; font-size: 1.1rem;">${product.name}</h3>
            <p style="color: #8d6e63; font-size: 0.85rem; margin-bottom: 1rem;">Per ${product.unit}</p>
            <div style="display: grid; gap: 0.75rem;">
                <div>
                    <label style="display: block; font-size: 0.8rem; color: #8d6e63; margin-bottom: 0.25rem; font-weight: 600;">Price (₱)</label>
                    <input type="number" id="price-${product.id}" value="${product.price}" style="width: 100%; padding: 0.5rem; border: 1px solid #d7ccc8; border-radius: 8px; font-size: 0.9rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.8rem; color: #8d6e63; margin-bottom: 0.25rem; font-weight: 600;">Stock</label>
                    <input type="number" id="stock-${product.id}" value="${product.stock}" style="width: 100%; padding: 0.5rem; border: 1px solid #d7ccc8; border-radius: 8px; font-size: 0.9rem;">
                </div>
                <button onclick="showProductUpdateModal(${product.id})" style="padding: 0.75rem; background: linear-gradient(135deg, #6d4c41 0%, #4e342e 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Update</button>
            </div>
        </div>
    `).join('');
}


// --- Product Management Modal Logic ---
let productIdToUpdate = null;

function showProductUpdateModal(productId) {
    productIdToUpdate = productId;
    document.getElementById('productUpdateModal').style.display = 'flex';
}

function closeProductUpdateModal() {
    document.getElementById('productUpdateModal').style.display = 'none';
    productIdToUpdate = null;
}

document.addEventListener('DOMContentLoaded', function() {
    const yesBtn = document.getElementById('productUpdateModalYes');
    if (yesBtn) {
        yesBtn.onclick = function() {
            if (productIdToUpdate) {
                updateProduct(productIdToUpdate);
                closeProductUpdateModal();
            }
        };
    }
});

function updateProduct(productId) {
    const price = document.getElementById('price-' + productId).value;
    const stock = document.getElementById('stock-' + productId).value;
    Promise.all([
        fetch('/products/' + productId + '/price', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ price: price })
        }),
        fetch('/products/' + productId + '/stock', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ stock: stock })
        })
    ]).then(() => {
        showProductSuccessModal();
        loadProductsManagement();
    }).catch(error => {
        console.error('Error updating product:', error);
        alert('Error updating product');
    });
}

function showProductSuccessModal() {
    document.getElementById('productSuccessModal').style.display = 'flex';
}

function closeProductSuccessModal() {
    document.getElementById('productSuccessModal').style.display = 'none';
}

function viewOrder(orderId) {
    window.open('/order/receipt/' + orderId, '_blank');
}

// Load products on page load
document.addEventListener('DOMContentLoaded', function() {
    loadProductsManagement();

    // Load reviews count + badge immediately (before user clicks the tab)
    loadAdminReviews();
});

// Admin Reviews Functions
async function loadAdminReviews() {
    const list = document.getElementById('adminReviewsList');
    list.innerHTML = '<div style="text-align: center; padding: 2rem; color: #8d6e63;">Loading reviews...</div>';

    try {
        const response = await fetch('/api/admin/reviews', {
            headers: { 'Accept': 'application/json' }
        });
        const data = await response.json();
        displayAdminReviews(data.reviews || []);
    } catch (error) {
        console.error('Error loading reviews:', error);
        list.innerHTML = '<div style="text-align: center; padding: 2rem; color: #c62828;">Failed to load reviews</div>';
    }
}

function displayAdminReviews(reviews) {
    const list = document.getElementById('adminReviewsList');
    const totalEl = document.getElementById('totalReviewsCount');
    const pendingEl = document.getElementById('pendingReviewsCount');
    const repliedEl = document.getElementById('repliedReviewsCount');
    const avgEl = document.getElementById('avgRatingDisplay');
    const badgeEl = document.getElementById('reviewsBadge');

    const total = reviews.length;
    const pending = reviews.filter(r => !r.admin_reply).length;
    const replied = reviews.filter(r => r.admin_reply).length;
    const avg = total > 0 ? (reviews.reduce((sum, r) => sum + r.rating, 0) / total).toFixed(1) : '0.0';

    if (totalEl) totalEl.textContent = total;
    if (pendingEl) pendingEl.textContent = pending;
    if (repliedEl) repliedEl.textContent = replied;
    if (avgEl) avgEl.textContent = avg;
    if (badgeEl) badgeEl.textContent = total;


    if (reviews.length === 0) {
        list.innerHTML = '<div style="text-align: center; padding: 3rem; color: #8d6e63;"><p style="font-size: 1rem;">No reviews yet</p><p style="font-size: 0.85rem;">Customer reviews will appear here</p></div>';
        return;
    }

    list.innerHTML = reviews.map(review => {
        const stars = Array(5).fill(0).map((_, i) =>
            `<span style="color: ${i < review.rating ? '#ffc107' : '#e0e0e0'}; font-size: 1rem;">★</span>`
        ).join('');

        const initials = review.customer && review.customer.name
            ? review.customer.name.charAt(0).toUpperCase()
            : '?';

        const productDisplay = review.product_slug
            ? review.product_slug.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ')
            : 'Unknown Product';

        const reviewDate = new Date(review.created_at).toLocaleDateString('en-US', {
            month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'
        });

        let replyHtml = '';
        if (review.admin_reply) {
            const replyDate = new Date(review.admin_reply_at).toLocaleDateString('en-US', {
                month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'
            });
            replyHtml = `
                <div style="background: #e8f5e9; border-left: 4px solid #51cf66; padding: 1rem; margin-top: 1rem; border-radius: 0 8px 8px 0;">
                    <div style="font-weight: 600; color: #2e7d32; font-size: 0.85rem; margin-bottom: 0.5rem;">Admin Reply <span style="font-weight: 400; color: #8d6e63;">- ${replyDate}</span></div>
                    <p style="color: #4e342e; font-size: 0.85rem; margin: 0;">${review.admin_reply}</p>
                </div>
            `;
        }

        return `
            <div style="background: white; border-radius: 12px; padding: 1.25rem; margin-bottom: 1rem; border: 1px solid #d7ccc8;" data-status="${review.admin_reply ? 'replied' : 'pending'}">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #6d4c41; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem;">${initials}</div>
                        <div>
                            <div style="font-weight: 600; color: #4e342e; font-size: 0.95rem;">${review.customer && review.customer.name ? review.customer.name : 'Unknown'}</div>
                            <div style="font-size: 0.75rem; color: #8d6e63;">${review.customer && review.customer.email ? review.customer.email : ''}</div>
                        </div>
                    </div>
                    <span style="background: #f5f0eb; padding: 0.35rem 0.75rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; color: #6d4c41;">${productDisplay}</span>
                </div>
                <div style="margin-bottom: 0.5rem;">${stars}</div>
                <p style="color: #4e342e; font-size: 0.9rem; margin-bottom: 0.5rem; line-height: 1.5;">${review.comment || 'No comment provided'}</p>
                <div style="font-size: 0.75rem; color: #a1887f; margin-bottom: 0.75rem;">${reviewDate}</div>
                ${replyHtml}
                <div style="margin-top: 1rem; display: flex; gap: 0.5rem; align-items: center;">
                    <textarea id="reply-${review.id}" placeholder="Write your reply..." style="flex: 1; padding: 0.75rem; border: 2px solid #d7ccc8; border-radius: 8px; font-size: 0.85rem; resize: none; min-height: 60px; font-family: inherit;">${review.admin_reply || ''}</textarea>
                    <button onclick="submitAdminReply(${review.id})" style="padding: 0.75rem 1.25rem; background: #6d4c41; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center;">${review.admin_reply ? 'Update' : 'Send'}</button>
                </div>
            </div>
        `;
    }).join('');
}

function filterAdminReviews(status) {
    document.querySelectorAll('.category-tabs .category-tab').forEach(tab => tab.classList.remove('active'));
    if (event && event.target) event.target.classList.add('active');

    const cards = document.querySelectorAll('#adminReviewsList > div');
    cards.forEach(card => {
        const statusAttr = card.getAttribute('data-status');
        if (status === 'all' || statusAttr === status) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

async function submitAdminReply(reviewId) {
    const reply = document.getElementById('reply-' + reviewId).value.trim();
    if (!reply) {
        alert('Please enter a reply message');
        return;
    }

    const button = event.target;
    button.disabled = true;
    button.innerHTML = '<span style="animation: spin 1s linear infinite;">⟳</span> Sending...';

    try {
        const response = await fetch(`/admin/reviews/${reviewId}/reply`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ admin_reply: reply })
        });

        const data = await response.json();
        if (data.success) {
            loadAdminReviews();
        } else {
            alert('Failed to submit reply');
            button.disabled = false;
            button.innerHTML = 'Send';
        }
    } catch (error) {
        alert('Error submitting reply');
        button.disabled = false;
        button.innerHTML = 'Send';
    }
}
</script>

@endsection

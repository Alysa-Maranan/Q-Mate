@extends('customer.layout')
@section('page-title', 'My Orders')
@section('active-nav', 'orders')
@section('customer-content')
<style>
.tabs { display: flex; border-bottom: 2px solid #f5f0eb; background: #faf8f6; }
.tab { flex: 1; padding: 1rem; text-align: center; font-weight: 600; font-size: 0.9rem; color: #8d6e63; transition: all 0.2s; border-bottom: 3px solid transparent; display: flex; align-items: center; justify-content: center; gap: 0.5rem; text-decoration: none; }
.tab:hover { background: #f5f0eb; color: #6d4c41; }
.tab.active { color: #6d4c41; background: white; border-bottom-color: #a1887f; }
.tab i { font-size: 1.1rem; }
.tab-content { display: none; }
.tab-content.active { display: block; }
.empty-state { text-align: center; padding: 4rem 2rem; color: #8d6e63; }
.empty-state i { font-size: 4rem; margin-bottom: 1rem; opacity: 0.5; }
.empty-state h3 { font-size: 1.2rem; margin-bottom: 0.5rem; color: #4e342e; }
.empty-state p { font-size: 0.9rem; margin-bottom: 1.5rem; }
.shop-btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #6d4c41 0%, #4e342e 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.2s; }
.shop-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(109,76,65,0.3); }

/* Order Card - Shopee Style */
.order-card { background: white; border-radius: 12px; border: 1px solid #e0e0e0; margin-bottom: 1rem; overflow: hidden; }
.order-card-header { padding: 1rem 1.25rem; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
.order-number { font-weight: 700; color: #4e342e; font-size: 0.9rem; }
.order-date { font-size: 0.8rem; color: #8d6e63; }
.order-status { padding: 0.4rem 0.8rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
.order-status.pending { background: #fff3e0; color: #e65100; }
.order-status.confirmed { background: #e3f2fd; color: #1565c0; }
.order-status.shipped { background: #f3e5f5; color: #7b1fa2; }
.order-status.completed { background: #e8f5e9; color: #2e7d32; }
.order-status.cancelled { background: #ffebee; color: #c62828; }
.order-card-body { padding: 1rem 1.25rem; }
.order-info-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
.order-info-label { font-size: 0.8rem; color: #8d6e63; }
.order-info-value { font-weight: 600; color: #4e342e; font-size: 0.9rem; }
.order-card-footer { padding: 1rem 1.25rem; background: #fafafa; border-top: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
.order-total { font-weight: 700; font-size: 1rem; color: #6d4c41; }
.order-actions { display: flex; gap: 0.5rem; }
.action-btn { padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s; text-decoration: none; }
.action-btn.primary { background: #6d4c41; color: white; border: none; }
.action-btn.primary:hover { background: #5d4037; }
.action-btn.secondary { background: white; color: #6d4c41; border: 1px solid #d7ccc8; }
.action-btn.secondary:hover { background: #f5f0eb; }
</style>

<div style="padding: 1.5rem;">
    <!-- Page Header -->
    <div style="background: linear-gradient(135deg, #6d4c41 0%, #4e342e 100%); border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 2rem; color: white;">
        <h1 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-shopping-bag"></i>
            My Orders
        </h1>
        <p style="font-size: 0.9rem; opacity: 0.9; margin: 0;">Track and manage your orders</p>
    </div>

    <div class="tabs">
        <a href="{{ route('customer.orders.all') }}" class="tab active">
            <i class="ph-bold ph-package"></i>
            <span>All Orders</span>
        </a>
        <a href="{{ route('customer.orders.topay') }}" class="tab">
            <i class="ph-bold ph-clock"></i>
            <span>To Pay</span>
        </a>
        <a href="{{ route('customer.orders.toship') }}" class="tab">
            <i class="ph-bold ph-truck"></i>
            <span>To Ship</span>
        </a>
        <a href="{{ route('customer.orders.completed') }}" class="tab">
            <i class="ph-bold ph-check-circle"></i>
            <span>Completed</span>
        </a>
        <a href="{{ route('customer.orders.cancelled') }}" class="tab">
            <i class="ph-bold ph-x-circle"></i>
            <span>Cancelled</span>
        </a>
    </div>

    <div class="tab-content active" style="padding: 2rem;">
        @if(isset($orders) && $orders->count() > 0)
            @foreach($orders as $order)
            <div class="order-card">
                <div class="order-card-header">
                    <div>
                        <span class="order-number">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                        <span class="order-date" style="margin-left: 0.5rem;">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <span class="order-status {{ $order->status }}">{{ ucfirst($order->status) }}</span>
                </div>
                <div class="order-card-body">
                    <div class="order-info-row">
                        <span class="order-info-label">Product</span>
                        <span class="order-info-value">{{ ucwords(str_replace('_', ' ', $order->product)) }}</span>
                    </div>
                    <div class="order-info-row" style="margin-bottom: 0;">
                        <span class="order-info-label">Quantity</span>
                        <span class="order-info-value">{{ $order->quantity ?? 1 }}</span>
                    </div>
                </div>
                <div class="order-card-footer">
                    <span class="order-total">Total: {{ $order->formatted_total }}</span>
                    <div class="order-actions">
                        <a href="/order/receipt/{{ $order->id }}" target="_blank" class="action-btn secondary">View Receipt</a>
                        @if($order->status == 'pending')
                        <button class="action-btn primary">Cancel Order</button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="empty-state">
                <i class="ph-bold ph-package"></i>
                <h3>No orders yet</h3>
                <p>Start shopping to see your orders here</p>
                <a href="{{ route('order') }}" class="shop-btn">
                    <i class="fas fa-shopping-bag"></i>
                    Shop Now
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
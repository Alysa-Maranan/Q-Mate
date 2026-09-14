@extends('customer/orders/layout')

@section('page-title', 'To Ship')
@section('active-tab', 'toship')

@section('content')
@php
    $toShipOrders = \App\Models\Order::where('customer_id', auth('customer')->id())
        ->where('status', 'to_ship')
        ->orderBy('created_at', 'desc')
        ->get();
@endphp

@if($toShipOrders->count() > 0)
    @foreach($toShipOrders as $order)
    <div style="background: white; border-radius: 4px; border: 1px solid #e0e0e0; margin-bottom: 1rem; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
        <!-- Header -->
        <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid #f5f5f5; display: flex; justify-content: space-between; align-items: center; background: #fafafa;">
            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem;">
                <span style="font-weight: 600; color: #333;">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                <span style="color: #ccc;">|</span>
                <span style="color: #888;"><i class="ph-bold ph-clock" style="margin-right: 0.25rem;"></i>{{ $order->created_at->format('M d, Y h:i A') }}</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div class="order-status-badge" style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase;
                    @if($order->status == 'confirmed') color: #2196f3;
                    @elseif($order->status == 'to_ship') color: #9c27b0;
                    @else color: #9c27b0; @endif">
                    {{ str_replace('_', ' ', ucfirst($order->status)) }}
                </div>
                @if($order->status == 'to_ship')
                <span style="background: #ff9800; color: white; font-size: 0.7rem; font-weight: 600; padding: 0.2rem 0.5rem; border-radius: 12px; text-transform: uppercase;">
                    <i class="ph-bold ph-truck" style="margin-right: 0.2rem;"></i> Out for Delivery
                </span>
                @endif
            </div>
        </div>

        <!-- Body -->
        <div style="padding: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 60px; height: 60px; background: #f9f9f9; border: 1px solid #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #ccc; overflow: hidden;">
                    <img src="{{ $order->product_image }}" alt="{{ $order->product }}" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div style="flex: 1;">
                    <div style="font-size: 1rem; color: #333; margin-bottom: 0.25rem; font-weight: 500;">Product: {{ ucwords(str_replace('_', ' ', $order->product)) }}</div>
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
                <a href="/order/receipt/{{ $order->id }}" target="_blank" style="padding: 0.4rem 1.2rem; border: 1px solid #ccc; color: #555; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 0.85rem; background: white; transition: all 0.2s;" onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background='white'">View Receipt</a>
            </div>
        </div>
    </div>
    @endforeach
@else
<div class="empty-state">
    <i class="ph-bold ph-truck"></i>
    <h3>No orders to ship</h3>
    <p>You don't have any orders ready for shipping.</p>
</div>
@endif
@endsection
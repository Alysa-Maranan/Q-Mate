@extends('customer/orders/layout')

@section('page-title', 'Cancelled')
@section('active-tab', 'cancelled')

@section('content')
@php
    $cancelledOrders = \App\Models\Order::where('customer_id', auth('customer')->id())
        ->where('status', 'cancelled')
        ->orderBy('created_at', 'desc')
        ->get();
@endphp

@if($cancelledOrders->count() > 0)
    @foreach($cancelledOrders as $order)
    <div style="background: white; border-radius: 4px; border: 1px solid #e0e0e0; margin-bottom: 1rem; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
        <!-- Header -->
        <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid #f5f5f5; display: flex; justify-content: space-between; align-items: center; background: #fafafa;">
            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem;">
                <span style="font-weight: 600; color: #333;">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                <span style="color: #ccc;">|</span>
                <span style="color: #888;"><i class="ph-bold ph-clock" style="margin-right: 0.25rem;"></i>{{ $order->created_at->format('M d, Y h:i A') }}</span>
            </div>
            <div class="order-status-badge" style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase; color: #ff424f;">
                Cancelled
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

        <!-- Cancellation Reason -->
        @if($order->cancellation_reason)
        <div style="padding: 0 1.25rem 1.25rem 1.25rem;">
            <div style="background: #fff3f3; border-left: 4px solid #ff424f; padding: 0.75rem 1rem; border-radius: 4px;">
                <p style="font-size: 0.75rem; color: #a1887f; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 0.25rem 0;">Cancellation Reason</p>
                <p style="font-size: 0.85rem; color: #4e342e; margin: 0; line-height: 1.4;">{{ $order->cancellation_reason }}</p>
            </div>
        </div>
        @endif
    </div>
    @endforeach
@else
<div class="empty-state">
    <i class="ph-bold ph-x-circle"></i>
    <h3>No cancelled orders</h3>
    <p>Your cancelled orders will appear here.</p>
</div>
@endif
@endsection
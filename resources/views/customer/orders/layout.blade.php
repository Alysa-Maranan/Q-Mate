@extends('customer.layout')

@section('active-nav', 'orders')

@section('header-action')
<style>
.content-header .content-title { display: none; }
.page-header {
    background: linear-gradient(135deg, #6d4c41 0%, #4e342e 100%);
    border-radius: 16px;
    padding: 1.5rem 2rem;
    margin-bottom: 2rem;
    color: white;
}
.page-title {
    font-size: 1.5rem;
    font-weight: 800;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.page-title i { font-size: 1.8rem; }
.subtitle { color: rgba(255,255,255,0.9); font-size: 0.9rem; margin: 0; }
</style>
@endsection

@section('customer-content')
<div style="padding: 1.5rem;">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">My Orders</h1>
        <p class="subtitle">Track and manage your orders</p>
    </div>

    @php
        $customerId = auth('customer')->id();

        $allOrdersCount = \App\Models\Order::where('customer_id', $customerId)->count();
        $cancelledOrdersCount = \App\Models\Order::where('customer_id', $customerId)
            ->where('status', 'cancelled')
            ->count();

        $toPayCount = \App\Models\Order::where('customer_id', $customerId)
            ->where('status', 'confirmed')
            ->count();

        $toShipCount = \App\Models\Order::where('customer_id', $customerId)
            ->where('status', 'to_ship')
            ->count();

        $completedOrdersCount = \App\Models\Order::where('customer_id', $customerId)
            ->where('status', 'completed')
            ->count();
    @endphp

    <div class="tabs">
        <a href="{{ route('customer.orders.all') }}" class="tab @if(View::yieldContent('active-tab') == 'all') active @endif">
            <i class="ph-bold ph-package"></i>
            <span>All Orders</span>
            <span class="tab-badge @if(View::yieldContent('active-tab') == 'all') badge-active @endif">{{ $allOrdersCount }}</span>
        </a>
        <a href="{{ route('customer.orders.topay') }}" class="tab @if(View::yieldContent('active-tab') == 'topay') active @endif">
            <i class="ph-bold ph-clock"></i>
            <span>To Pay</span>
            <span class="tab-badge @if(View::yieldContent('active-tab') == 'topay') badge-active @endif">{{ $toPayCount }}</span>
        </a>
        <a href="{{ route('customer.orders.toship') }}" class="tab @if(View::yieldContent('active-tab') == 'toship') active @endif">
            <i class="ph-bold ph-truck"></i>
            <span>To Ship</span>
            <span class="tab-badge @if(View::yieldContent('active-tab') == 'toship') badge-active @endif">{{ $toShipCount }}</span>
        </a>
        <a href="{{ route('customer.orders.completed') }}" class="tab @if(View::yieldContent('active-tab') == 'completed') active @endif">
            <i class="ph-bold ph-check-circle"></i>
            <span>Completed</span>
            <span class="tab-badge @if(View::yieldContent('active-tab') == 'completed') badge-active @endif">{{ $completedOrdersCount }}</span>
        </a>
        <a href="{{ route('customer.orders.cancelled') }}" class="tab @if(View::yieldContent('active-tab') == 'cancelled') active @endif">
            <i class="ph-bold ph-x-circle"></i>
            <span>Cancelled</span>
            <span class="tab-badge @if(View::yieldContent('active-tab') == 'cancelled') badge-active @endif">{{ $cancelledOrdersCount }}</span>
        </a>
    </div>

    <style>
        .tab-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            height: 20px;
            padding: 0 8px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 800;
            background: rgba(161, 136, 127, 0.18);
            color: #6d4c41;
            border: 1px solid rgba(161, 136, 127, 0.25);
        }
        .tab-badge.badge-active {
            background: #6d4c41;
            color: #fff;
            border-color: #6d4c41;
        }
    </style>

<div class="tab-content" style="display: block; padding: 2rem 0;">
    @yield('content')
</div>
</div>
@endsection
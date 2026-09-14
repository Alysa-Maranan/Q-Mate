@extends('customer.layout')
@section('page-title', '')
@section('active-nav', 'dashboard')
@section('styles')
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
@section('customer-content')
<div class="p-6">

            <div id="dashboardSection">
            
            @if(session('success'))
            <div style="background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); border: 2px solid #4caf50; color: #2e7d32; padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 2px 8px rgba(76,175,80,0.2);">
                <i class="fas fa-check-circle" style="font-size: 1.5rem;"></i>
                <span style="font-weight: 600;">{{ session('success') }}</span>
            </div>
            @endif

            <!-- Page Header -->
            <div style="background: linear-gradient(135deg, #6d4c41 0%, #4e342e 100%); border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 2rem; color: white;">
                <h1 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.75rem;">
                    Dashboard
                </h1>
                <p style="font-size: 0.9rem; opacity: 0.9; margin: 0; display: flex; gap: 1.5rem; align-items: center;">
                    <span><i class="fas fa-shopping-bag" style="margin-right: 0.25rem;"></i> Orders this month: <strong>{{ $recentOrders->count() }}</strong></span>
                    <span><i class="fas fa-box" style="margin-right: 0.25rem;"></i> Total Orders: <strong>{{ $totalOrders ?? $recentOrders->count() }}</strong></span>
                </p>
            </div>

            <!-- My Purchases (Shopee Style) -->
            <div class="bg-white rounded-2xl border border-[#eaddd7] shadow-sm mb-8 overflow-hidden">
                <div class="flex justify-between items-center p-5 border-b border-[#f5f0eb]">
                    <h3 class="text-lg font-bold text-[#4e342e]">My Purchases</h3>
                    <a href="{{ route('customer.orders.all') }}" class="text-sm font-medium text-[#8d6e63] hover:text-[#6d4c41] transition">View Purchase History →</a>
                </div>
                <div class="p-6 grid grid-cols-4 sm:grid-cols-4 gap-4 text-center">
                    <a href="{{ route('customer.orders.topay') ?? '#' }}" class="group flex flex-col items-center gap-3 text-[#6d4c41] hover:text-[#4e342e] transition">
                        <div class="relative w-12 h-12 flex justify-center items-center rounded-full bg-[#f8f5f1] group-hover:bg-[#efebe9] transition">
                            <i class="fas fa-wallet text-xl"></i>
                            @if($confirmedOrders > 0)
                            <span class="absolute -top-1 -right-1 bg-[#d32f2f] text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-white">{{ $confirmedOrders }}</span>
                            @endif
                        </div>
                        <span class="text-sm font-semibold">To Pay</span>
                    </a>
                    <a href="{{ route('customer.orders.toship') ?? '#' }}" class="group flex flex-col items-center gap-3 text-[#6d4c41] hover:text-[#4e342e] transition">
                        <div class="relative w-12 h-12 flex justify-center items-center rounded-full bg-[#f8f5f1] group-hover:bg-[#efebe9] transition">
                            <i class="fas fa-box text-xl"></i>
                            @if($readyOrders > 0)
                            <span class="absolute -top-1 -right-1 bg-[#ffa500] text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-white">{{ $readyOrders }}</span>
                            @endif
                        </div>
                        <span class="text-sm font-semibold">To Ship</span>
                    </a>
                    <a href="{{ route('customer.orders.completed') ?? '#' }}" class="group flex flex-col items-center gap-3 text-[#6d4c41] hover:text-[#4e342e] transition">
                        <div class="relative w-12 h-12 flex justify-center items-center rounded-full bg-[#f8f5f1] group-hover:bg-[#efebe9] transition">
                            <i class="fas fa-star text-xl"></i>
                            @if($completedOrders > 0)
                            <span class="absolute -top-1 -right-1 bg-[#26aa99] text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-white">{{ $completedOrders }}</span>
                            @endif
                        </div>
                        <span class="text-sm font-semibold">Completed</span>
                    </a>
                    <a href="{{ route('customer.orders.cancelled') ?? '#' }}" class="group flex flex-col items-center gap-3 text-[#6d4c41] hover:text-[#4e342e] transition">
                        <div class="relative w-12 h-12 flex justify-center items-center rounded-full bg-[#f8f5f1] group-hover:bg-[#efebe9] transition">
                            <i class="fas fa-times-circle text-xl"></i>
                            @if($cancelledOrders > 0)
                            <span class="absolute -top-1 -right-1 bg-[#ff424f] text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-white">{{ $cancelledOrders }}</span>
                            @endif
                        </div>
                        <span class="text-sm font-semibold">Cancelled</span>
                    </a>
                </div>
            </div>

            <!-- Aesthetic Overview Grid (Rare Beauty minimalist style) -> Removed as per request -->

            <!-- Order Trends Chart -> Removed as per request -->

            <!-- Recent Orders -->
            <div class="mb-10">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 800; color: #4e342e; display: flex; align-items: center; gap: 0.75rem; margin: 0;">
                        <i class="fas fa-history" style="font-size: 1.4rem; color: #6d4c41;"></i>
                        Recent Orders
                    </h3>
                    <a href="{{ route('customer.orders.all') }}" style="color: #6d4c41; font-size: 0.9rem; font-weight: 700; text-decoration: none; padding: 0.5rem 1rem; background: white; border-radius: 8px; border: 1px solid #ddd; transition: all 0.2s;" onmouseover="this.style.background='#f9f9f9';" onmouseout="this.style.background='white';">View All →</a>
                </div>

                @if($recentOrders->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentOrders as $order)
                        <div style="background: white; border-radius: 4px; border: 1px solid #e0e0e0; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
                            <!-- Header -->
                            <div style="padding: 0.75rem 1.25rem; border-bottom: 1px solid #f5f5f5; display: flex; justify-content: space-between; align-items: center; background: #fafafa;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem;">
                                    <span style="font-weight: 600; color: #333;">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                                    <span style="color: #ccc;">|</span>
                                    <span style="color: #888;">{{ $order->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div style="font-size: 0.85rem; font-weight: 600; text-transform: uppercase;
                                        @if($order->status == 'pending') color: #ee4d2d;
                                        @elseif($order->status == 'confirmed') color: #2196f3;
                                        @elseif($order->status == 'to_ship') color: #9c27b0;
                                        @elseif($order->status == 'completed') color: #26aa99;
                                        @else color: #ff424f; @endif">
                                        {{ str_replace('_', ' ', ucfirst($order->status)) }}
                                    </div>
                                    @if($order->status == 'to_ship')
                                    <span style="background: #ff9800; color: white; font-size: 0.65rem; font-weight: 600; padding: 0.15rem 0.4rem; border-radius: 10px; text-transform: uppercase;">
                                        <i class="ph-bold ph-truck" style="margin-right: 0.15rem;"></i> Out for Delivery
                                    </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Body -->
                            <div style="padding: 1.25rem;">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div style="width: 60px; height: 60px; background: #f9f9f9; border: 1px solid #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
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
                                    Total: <span style="font-weight: 600; font-size: 1rem; color: #6d4c41;">{{ $order->formatted_total }}</span>
                                </div>
                                @if($order->status !== 'cancelled')
                                <a href="/order/receipt/{{ $order->id }}" target="_blank" style="padding: 0.4rem 1.2rem; border: 1px solid #ccc; color: #555; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 0.85rem; background: white; transition: all 0.2s;" onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background='white'">View Receipt</a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 3rem 1rem; background: white; border-radius: 4px; border: 1px solid #e0e0e0;">
                        <i class="fas fa-box-open" style="font-size: 3rem; color: #ddd; margin-bottom: 1rem; display: block;"></i>
                        <h3 style="font-weight: 700; color: #999; margin: 0.5rem 0;">No orders yet</h3>
                        <p style="color: #aaa; font-size: 0.9rem; margin-bottom: 1.5rem;">Start shopping to see your orders here</p>
                        <a href="{{ route('order') }}" style="display: inline-block; background: #6d4c41; color: white; padding: 0.6rem 1.5rem; border-radius: 4px; font-weight: 600; text-decoration: none; font-size: 0.9rem;">Shop Now</a>
                    </div>
                @endif
            </div>

            </div>

@endsection
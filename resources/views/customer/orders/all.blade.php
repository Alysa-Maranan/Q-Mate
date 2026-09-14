@extends('customer/orders/layout')

@section('page-title', 'All Orders')
@section('active-tab', 'all')

@section('content')
<style>
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: 2000;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(5px);
    }
    
    .modal-overlay.show {
        display: flex;
    }
    
    .modal {
        background: white;
        padding: 2.5rem;
        border-radius: 20px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
        text-align: center;
        position: relative;
        animation: modalSlideIn 0.3s ease-out;
    }
    
    .modal::-webkit-scrollbar {
        width: 8px;
    }
    
    .modal::-webkit-scrollbar-track {
        background: #f5f0eb;
        border-radius: 10px;
    }
    
    .modal::-webkit-scrollbar-thumb {
        background: #a1887f;
        border-radius: 10px;
    }
    
    .modal::-webkit-scrollbar-thumb:hover {
        background: #6d4c41;
    }
    
    @keyframes modalSlideIn {
        from { opacity: 0; transform: translateY(-30px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    
    .modal-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #8d6e63;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 50%;
        transition: all 0.2s;
    }
    
    .modal-close:hover {
        background: #f5f0eb;
        color: #4e342e;
    }
    
    .modal-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #6d4c41 0%, #4e342e 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2.5rem;
        color: white;
        box-shadow: 0 8px 25px rgba(109, 76, 65, 0.3);
    }
    
    .modal-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #4e342e;
        margin-bottom: 1rem;
    }
    
    .modal-body {
        margin-bottom: 2rem;
        text-align: left;
    }
    
    .modal-body p {
        font-size: 0.95rem;
        color: #8d6e63;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-label {
        display: block;
        font-weight: 600;
        color: #4e342e;
        margin-bottom: 0.6rem;
        font-size: 0.9rem;
    }
    
    .form-input {
        width: 100%;
        padding: 0.85rem;
        border: 2px solid rgba(161,136,127,0.2);
        border-radius: 10px;
        font-family: 'Inter', sans-serif;
        font-size: 0.95rem;
        color: #4e342e;
        resize: vertical;
        min-height: 120px;
        transition: border-color 0.2s;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #6d4c41;
    }
    
    .form-input::placeholder {
        color: #a1887f;
        font-style: italic;
    }
    
    .modal-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
    }
    
    .modal-btn {
        flex: 1;
        padding: 0.85rem 1.8rem;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s;
        font-family: 'Inter', sans-serif;
    }
    
    .modal-btn.primary {
        background: #6d4c41;
        color: white;
        box-shadow: 0 4px 15px rgba(109, 76, 65, 0.3);
    }
    
    .modal-btn.primary:hover {
        background: #5d4037;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(109, 76, 65, 0.4);
    }
    
    .modal-btn.secondary {
        background: #f5f0eb;
        color: #6d4c41;
        border: 2px solid rgba(161,136,127,0.2);
    }
    
    .modal-btn.secondary:hover {
        background: #efebe9;
        border-color: #6d4c41;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(109,76,65,0.15);
    }
    
    .error-text {
        color: #f44336;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        font-weight: 500;
    }
</style>

@if($orders->count() > 0)
    @foreach($orders as $order)
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
                @elseif($order->status == 'cancelled') color: #ff424f;
                @else color: #ff424f; @endif">
                {{ str_replace('_', ' ', ucfirst($order->status)) }}
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
                @if($order->status !== 'cancelled')
                <a href="/order/receipt/{{ $order->id }}" target="_blank" style="padding: 0.4rem 1.2rem; border: 1px solid #ccc; color: #555; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 0.85rem; background: white; transition: all 0.2s;" onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background='white'">View Receipt</a>
                @endif

                @if($order->status === 'pending')
                <button onclick="showCancelModalForOrder({{ $order->id }})" style="padding: 0.4rem 1.2rem; background: white; color: #ee4d2d; border: 1px solid #ee4d2d; border-radius: 4px; font-weight: 500; font-size: 0.85rem; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#fff0f0'" onmouseout="this.style.background='white'">Cancel Order</button>
                @endif
            </div>
        </div>
    </div>
    @endforeach
@else
<div class="empty-state">
    <i class="ph-bold ph-package"></i>
    <h3>No orders yet</h3>
    <p>You haven't placed any orders. Start shopping now!</p>
    <a href="{{ route('order') }}" class="shop-btn">
        <i class="ph-bold ph-storefront"></i>
        Shop Now
    </a>
</div>
@endif

<!-- Cancel Order Modal -->
<div class="modal-overlay" id="cancelModal">
    <div class="modal">
        <button class="modal-close" onclick="hideCancelModal()"><i class="ph-bold ph-x"></i></button>
        <div class="modal-icon">
            <i class="ph-bold ph-warning"></i>
        </div>
        <h2 class="modal-title">Cancel Order</h2>
        
        <form method="POST" id="cancelForm">
            @csrf
            <div class="modal-body">
                <p>Please tell us why you're cancelling this order. Your feedback helps us improve.</p>
                
                <div class="form-group">
                    <label class="form-label">Reason for Cancellation</label>
                    <textarea name="cancellation_reason" class="form-input" placeholder="E.g., Changed my mind, found another supplier, price too high..." required minlength="10"></textarea>
                    <div class="error-text" id="reasonError"></div>
                </div>
            </div>
            
            <div class="modal-buttons">
                <button type="button" onclick="hideCancelModal()" class="modal-btn secondary">Keep Order</button>
                <button type="submit" class="modal-btn primary">Cancel Order</button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentOrderId = null;
    
    function showCancelModalForOrder(orderId) {
        currentOrderId = orderId;
        document.getElementById('cancelForm').action = '/order/' + orderId + '/cancel';
        document.getElementById('cancelModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    
    function hideCancelModal() {
        document.getElementById('cancelModal').classList.remove('show');
        document.body.style.overflow = 'auto';
        document.getElementById('reasonError').textContent = '';
        document.querySelector('textarea[name="cancellation_reason"]').value = '';
    }
    
    // Close modal when clicking outside
    document.getElementById('cancelModal').addEventListener('click', function(e) {
        if (e.target === this) {
            hideCancelModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideCancelModal();
        }
    });
    
    // Validate form and submit via AJAX
    document.getElementById('cancelForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const reason = document.querySelector('textarea[name="cancellation_reason"]').value.trim();
        if (reason.length < 10) {
            document.getElementById('reasonError').textContent = 'Please provide at least 10 characters.';
            return;
        }

        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Cancelling...';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            hideCancelModal();

            if (data.success) {
                showCancellationSuccessModal();
            } else {
                alert(data.message || 'Failed to cancel order. Please try again.');
            }
        } catch (error) {
            console.error('Error cancelling order:', error);
            alert('An error occurred. Please try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });

    // Success Modal for cancellation
    function showCancellationSuccessModal() {
        let modal = document.getElementById('cancellationSuccessModal');
        if (!modal) {
            // Create modal if it doesn't exist
            const modalHtml = `
                <div class="modal-overlay" id="cancellationSuccessModal">
                    <div class="modal" style="max-width: 420px;">
                        <div class="modal-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                            <i class="ph-bold ph-check" style="font-size: 2.5rem;"></i>
                        </div>
                        <h2 class="modal-title">Order Cancelled</h2>
                        <p style="color: #8d6e63; margin-bottom: 1.5rem; line-height: 1.6;">Your order has been cancelled successfully. If you have any questions, feel free to contact us.</p>
                        <div class="modal-buttons">
                            <button type="button" onclick="document.getElementById('cancellationSuccessModal').classList.remove('show'); document.body.style.overflow = 'auto';" class="modal-btn primary">OK, Got it</button>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            modal = document.getElementById('cancellationSuccessModal');
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('show');
                    document.body.style.overflow = 'auto';
                    location.reload();
                }
            });
        }
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    // Real-time order status updates
    let statusCheckInterval;
    
    function checkOrderStatuses() {
        const orderElements = document.querySelectorAll('[data-order-id]');
        orderElements.forEach(async (element) => {
            const orderId = element.dataset.orderId;
            try {
                const response = await fetch(`/api/customer/order-status/${orderId}`);
                if (response.ok) {
                    const data = await response.json();
                    const statusBadge = element.querySelector('.order-status-badge');
                    if (statusBadge && statusBadge.textContent.toLowerCase() !== data.status) {
                        // Status changed, reload page to show updated status
                        location.reload();
                    }
                }
            } catch (error) {
                console.error('Failed to check order status:', error);
            }
        });
    }
    
    // Check order statuses every 15 seconds
    if (document.querySelectorAll('[data-order-id]').length > 0) {
        statusCheckInterval = setInterval(checkOrderStatuses, 15000);
    }
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        if (statusCheckInterval) clearInterval(statusCheckInterval);
    });
</script>

@endsection

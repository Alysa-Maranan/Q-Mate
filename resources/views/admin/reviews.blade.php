@extends('layouts.app')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<style>
    .sidebar-content-wrap {
        margin-left: 300px;
        transition: margin-left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        padding-top: 0;
    }
    .sidebar-content-wrap.expanded {
        margin-left: 0;
        padding-top: 80px;
    }
    @media (max-width: 768px) {
        .sidebar-content-wrap { margin-left: 0 !important; padding-top: 70px; }
    }
    .reviews-container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
    .reviews-header { background: linear-gradient(135deg, #6d4c41 0%, #4e342e 100%); border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 2rem; color: white; }
    .reviews-header h1 { font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.75rem; }
    .reviews-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    .stat-card { background: white; border-radius: 12px; padding: 1.25rem; border: 1px solid rgba(161,136,127,0.2); }
    .stat-card h3 { font-size: 0.85rem; color: #8d6e63; margin-bottom: 0.5rem; }
    .stat-card .value { font-size: 1.8rem; font-weight: 700; color: #4e342e; }
    .stat-card .value.pending { color: #e65100; }
    .stat-card .value.replied { color: #2e7d32; }
    .review-card { background: white; border-radius: 12px; border: 1px solid rgba(161,136,127,0.2); margin-bottom: 1rem; overflow: hidden; }
    .review-card-header { padding: 1rem 1.25rem; background: #faf8f6; border-bottom: 1px solid #efebe9; display: flex; justify-content: space-between; align-items: center; }
    .review-customer { display: flex; align-items: center; gap: 0.75rem; }
    .review-avatar { width: 40px; height: 40px; border-radius: 50%; background: #6d4c41; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; }
    .review-info h4 { color: #4e342e; font-size: 0.95rem; margin-bottom: 0.15rem; }
    .review-info span { font-size: 0.75rem; color: #8d6e63; }
    .review-product { background: #f5f0eb; padding: 0.35rem 0.75rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; color: #6d4c41; }
    .review-card-body { padding: 1rem 1.25rem; }
    .review-rating { color: #ffc107; font-size: 1rem; display: flex; gap: 2px; margin-bottom: 0.5rem; }
    .review-comment { color: #4e342e; font-size: 0.9rem; line-height: 1.5; margin-bottom: 0.75rem; }
    .review-date { font-size: 0.75rem; color: #a1887f; }
    .review-reply { background: #f0f7f0; border-left: 4px solid #51cf66; padding: 1rem; margin-top: 1rem; border-radius: 0 8px 8px 0; }
    .review-reply-header { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; font-weight: 600; color: #2e7d32; font-size: 0.85rem; }
    .review-reply-text { color: #4e342e; font-size: 0.85rem; line-height: 1.4; }
    .review-reply-date { font-size: 0.7rem; color: #8d6e63; margin-top: 0.25rem; }
    .review-card-footer { padding: 1rem 1.25rem; background: #fafafa; border-top: 1px solid #efebe9; }
    .reply-form { display: flex; gap: 0.75rem; align-items: flex-start; }
    .reply-form textarea { flex: 1; padding: 0.75rem; border: 2px solid rgba(161,136,127,0.2); border-radius: 8px; font-family: 'Inter', sans-serif; font-size: 0.85rem; resize: none; min-height: 60px; }
    .reply-form textarea:focus { border-color: #6d4c41; outline: none; }
    .reply-form button { padding: 0.75rem 1.5rem; background: #6d4c41; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; }
    .reply-form button:hover { background: #5d4037; }
    .reply-form button:disabled { opacity: 0.5; cursor: not-allowed; }
    .no-reviews { text-align: center; padding: 4rem 2rem; color: #8d6e63; }
    .no-reviews i { font-size: 4rem; margin-bottom: 1rem; opacity: 0.5; }
    .no-reviews h3 { font-size: 1.2rem; color: #4e342e; margin-bottom: 0.5rem; }
    .filter-tabs { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; }
    .filter-tab { padding: 0.6rem 1.2rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; border: 2px solid transparent; background: white; color: #8d6e63; transition: all 0.2s; }
    .filter-tab:hover, .filter-tab.active { background: #6d4c41; color: white; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/@phosphor-icons/web"></script>
@endpush

@section('content')
@include('components.sidebar')

<div id="main-content-wrap" class="sidebar-content-wrap">
    {{-- Success Modal --}}
    <div id="replySuccessModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.35); display:none; align-items:center; justify-content:center; z-index: 9999;">
        <div style="background:white; border-radius: 16px; width: min(520px, 92vw); padding: 1.5rem 1.25rem; box-shadow: 0 10px 40px rgba(0,0,0,0.25); border: 1px solid rgba(161,136,127,0.2);">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <div style="width:48px; height:48px; border-radius:50%; background:#e8f5e9; border:2px solid #4caf50; display:flex; align-items:center; justify-content:center; color:#2e7d32; font-size:1.4rem;">
                    <i class="ph-bold ph-check-circle"></i>
                </div>
                <div>
                    <div style="font-weight:800; color:#2e7d32; font-size:1.1rem;">Reply Sent</div>
                    <div style="color:#4e342e; font-size:0.9rem; margin-top:0.15rem;">Your admin response has been saved.</div>
                </div>
            </div>
            <div style="display:flex; justify-content:flex-end; margin-top: 1.25rem;">
                <button type="button" onclick="closeReplySuccessModal()" style="background:#6d4c41; color:white; border:none; border-radius:10px; padding:0.6rem 1rem; font-weight:700; cursor:pointer;">
                    OK
                </button>
            </div>
        </div>
    </div>

    <div class="reviews-container">
        <div class="reviews-header">
            <h1><i class="ph-bold ph-star"></i> Customer Reviews</h1>
            <p style="font-size: 0.9rem; opacity: 0.9; margin: 0;">Manage and respond to customer product reviews</p>
        </div>

        <div class="reviews-stats">
            <div class="stat-card">
                <h3>Total Reviews</h3>
                <div class="value">{{ $reviews->count() }}</div>
            </div>
            <div class="stat-card">
                <h3>Pending Reply</h3>
                <div class="value pending">{{ $reviews->whereNull('admin_reply')->count() }}</div>
            </div>
            <div class="stat-card">
                <h3>Replied</h3>
                <div class="value replied">{{ $reviews->whereNotNull('admin_reply')->count() }}</div>
            </div>
            <div class="stat-card">
                <h3>Average Rating</h3>
                <div class="value">{{ $reviews->count() > 0 ? number_format($reviews->avg('rating'), 1) : '0.0' }}</div>
            </div>
        </div>

        <div class="filter-tabs">
            <button class="filter-tab active" onclick="filterReviews('all')">All</button>
            <button class="filter-tab" onclick="filterReviews('pending')">Pending Reply</button>
            <button class="filter-tab" onclick="filterReviews('replied')">Replied</button>
        </div>

        <div id="reviewsList">
            @forelse($reviews as $review)
            <div class="review-card" data-status="{{ $review->admin_reply ? 'replied' : 'pending' }}">
                <div class="review-card-header">
                    <div class="review-customer">
                        <div class="review-avatar">{{ strtoupper(substr($review->customer->name, 0, 1)) }}</div>
                        <div class="review-info">
                            <h4>{{ $review->customer->name }}</h4>
                            <span>{{ $review->customer->email }}</span>
                        </div>
                    </div>
                    <span class="review-product">{{ ucwords(str_replace('-', ' ', $review->product_slug)) }}</span>
                </div>
                <div class="review-card-body">
                    <div class="review-rating">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="ph-bold ph-star" style="{{ $i <= $review->rating ? 'color:#ffc107;' : 'color:#e0e0e0;' }}"></i>
                        @endfor
                    </div>
                    <p class="review-comment">{{ $review->comment ?: 'No comment provided' }}</p>
                    <span class="review-date">{{ $review->created_at->format('M d, Y h:i A') }}</span>

                    @if($review->admin_reply)
                    <div class="review-reply">
                        <div class="review-reply-header">
                            <i class="ph-bold ph-check-circle"></i> Admin Reply
                            <span class="review-reply-date"> - {{ $review->admin_reply_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <p class="review-reply-text">{{ $review->admin_reply }}</p>
                    </div>
                    @endif
                </div>
                <div class="review-card-footer">
                    @if(!$review->admin_reply)
                    <div class="reply-form">
                        <textarea id="reply-{{ $review->id }}" placeholder="Write your reply..."></textarea>
                        <button onclick="submitReply({{ $review->id }}, this)">
                            <i class="ph-bold ph-paper-plane-tilt"></i> Send
                        </button>
                    </div>
                    @else
                    <div class="reply-form">
                        <textarea id="reply-{{ $review->id }}" placeholder="Update your reply...">{{ $review->admin_reply }}</textarea>
                        <button onclick="submitReply({{ $review->id }}, this)">Update</button>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="no-reviews">
                <i class="ph-bold ph-star"></i>
                <h3>No reviews yet</h3>
                <p>Customer reviews will appear here</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<script>
function filterReviews(status) {
    document.querySelectorAll('.filter-tab').forEach(tab => tab.classList.remove('active'));
    event.target.classList.add('active');

    document.querySelectorAll('.review-card').forEach(card => {
        if (status === 'all' || card.dataset.status === status) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

async function submitReply(reviewId, buttonEl) {
    const reply = document.getElementById('reply-' + reviewId).value.trim();
    if (!reply) {
        alert('Please enter a reply message');
        return;
    }

    const button = buttonEl || (window.event ? window.event.target : null);
    if (button) {
        button.disabled = true;
        button.innerHTML = '<i class="ph-bold ph-spinner" style="animation: spin 1s linear infinite;"></i> Sending...';
    }

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
            showReplySuccessModal();
            // Reload after a short delay to allow user to see the modal
            setTimeout(() => location.reload(), 900);
        } else {
            alert('Failed to submit reply');
            button.disabled = false;
            button.innerHTML = '<i class="ph-bold ph-paper-plane-tilt"></i> Send';
        }
    } catch (error) {
        alert('Error submitting reply');
        button.disabled = false;
        button.innerHTML = '<i class="ph-bold ph-paper-plane-tilt"></i> Send';
    }
}

function showReplySuccessModal() {
    const modal = document.getElementById('replySuccessModal');
    if (!modal) return;
    modal.style.display = 'flex';
}

function closeReplySuccessModal() {
    const modal = document.getElementById('replySuccessModal');
    if (!modal) return;
    modal.style.display = 'none';
}
</script>
@endsection

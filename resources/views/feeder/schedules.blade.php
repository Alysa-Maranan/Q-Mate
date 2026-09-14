<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Feeding Schedules</title>
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="{{ asset('notification-styles.css') }}">
</head>
<body>
<div class="container">
    <h1>Feeding Schedules</h1>
    <!-- success message -->
    @if(session('success'))
        <div style="padding:10px;margin-bottom:10px;background:#e6ffed;border:1px solid #86efac;border-radius:6px;color:#065f46">{{ session('success') }}</div>
    @endif

    <!-- validation errors -->
    @if($errors->any())
        <div style="padding:10px;margin-bottom:10px;background:#fff1f2;border:1px solid #fca5a5;border-radius:6px;color:#7f1d1d">
            <strong>There were some problems with your input:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ url('/feeder/schedules') }}">
        @csrf
        <label>Time</label>
        <input type="time" name="time" required value="{{ old('time') }}">

        <label>Days</label>
        <div>
            @php $oldDays = old('days', []); @endphp
            <label><input type="checkbox" name="days[]" value="mon" {{ in_array('mon', $oldDays) ? 'checked' : '' }}> Mon</label>
            <label><input type="checkbox" name="days[]" value="tue" {{ in_array('tue', $oldDays) ? 'checked' : '' }}> Tue</label>
            <label><input type="checkbox" name="days[]" value="wed" {{ in_array('wed', $oldDays) ? 'checked' : '' }}> Wed</label>
            <label><input type="checkbox" name="days[]" value="thu" {{ in_array('thu', $oldDays) ? 'checked' : '' }}> Thu</label>
            <label><input type="checkbox" name="days[]" value="fri" {{ in_array('fri', $oldDays) ? 'checked' : '' }}> Fri</label>
            <label><input type="checkbox" name="days[]" value="sat" {{ in_array('sat', $oldDays) ? 'checked' : '' }}> Sat</label>
            <label><input type="checkbox" name="days[]" value="sun" {{ in_array('sun', $oldDays) ? 'checked' : '' }}> Sun</label>
        </div>

        <label>Amount</label>
        <input type="number" name="amount" min="1" value="{{ old('amount', 1) }}">

        <button type="submit" class="add-schedule-btn" style="background:#a1887f;color:#fffdfa;font-size:1.1rem;font-weight:600;padding:0.85rem 0;border-radius:10px;box-shadow:0 2px 8px rgba(141,110,99,0.10);transition:background 0.2s;letter-spacing:0.5px;" onmouseover="this.style.background='#8d6e63'" onmouseout="this.style.background='#a1887f'">➕ Add Schedule</button>
    </form>

    <h2>Existing</h2>
    <ul id="schedules-list">
        @foreach($schedules as $s)
            <li data-id="{{ $s->id }}">
                <span class="sched-info">{{ $s->time }} — {{ implode(',', $s->days ?? []) }} — x{{ $s->amount }}</span>
                <form method="POST" action="{{ url('/feeder/schedules/'.$s->id) }}" style="display:inline" class="delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
                <button class="ajax-trigger" data-id="{{ $s->id }}" data-amount="{{ $s->amount }}">Trigger Now</button>
                <span class="trigger-status" style="margin-left:.5rem"></span>
            </li>
        @endforeach
    </ul>

    <script>
        // CSRF token for AJAX
        const csrf = '{{ csrf_token() }}';

        async function triggerSchedule(id, amount, btn) {
            const statusEl = btn.parentElement.querySelector('.trigger-status');
            statusEl.textContent = 'Sending...';
            try {
                const res = await fetch('/feeder/schedules/' + id + '/trigger', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                });
                if (!res.ok) throw new Error('Network error');
                const j = await res.json();
                if (j.status === 'ok') {
                    statusEl.textContent = 'Triggered ✓';
                    
                    // Add notification
                    if (window.notify) {
                        window.notify.feeder(`Feeder triggered for ${amount}s`, '🍽️');
                    }
                } else {
                    statusEl.textContent = 'Error';
                    
                    // Add error notification
                    if (window.notify) {
                        window.notify.error('Failed to trigger feeder', '✕');
                    }
                }
            } catch (err) {
                statusEl.textContent = 'Failed';
                
                // Add error notification
                if (window.notify) {
                    window.notify.error('Connection error: ' + err.message, '✕');
                }
            }
            setTimeout(() => { statusEl.textContent = ''; }, 3000);
        }

        document.addEventListener('click', (e) => {
            if (e.target && e.target.classList.contains('ajax-trigger')) {
                const id = e.target.getAttribute('data-id');
                const amount = e.target.getAttribute('data-amount') || 1;
                triggerSchedule(id, amount, e.target);
            }
        });

        // Add confirmation for delete forms
        document.querySelectorAll('.delete-form').forEach(f => {
            f.addEventListener('submit', function(ev){
                if (!confirm('Are you sure you want to delete this schedule?')) {
                    ev.preventDefault();
                }
            });
        });
    </script>

    <!-- Notification System Scripts -->
    <script src="{{ asset('notification-system.js') }}"></script>
    <script src="{{ asset('notification-helpers.js') }}"></script>
</div>
</body>
</html>

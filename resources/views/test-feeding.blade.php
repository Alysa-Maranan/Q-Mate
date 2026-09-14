@extends('layouts.app')

@section('content')
<div style="padding:3rem;max-width:800px;margin:0 auto;">
    <h1 style="color:#6d4c41;margin-bottom:2rem;">🧪 Test Animation & Notification</h1>
    
    <div style="background:white;padding:2rem;border-radius:16px;margin-bottom:2rem;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
        <h2 style="color:#6d4c41;margin-bottom:1rem;">Step 1: Trigger Feeding</h2>
        <button onclick="triggerFeeding()" style="background:#4caf50;color:white;border:none;padding:1rem 2rem;border-radius:8px;font-size:1.1rem;cursor:pointer;font-weight:600;">
            🍽️ Start Feeding (10 seconds)
        </button>
    </div>
    
    <div style="background:white;padding:2rem;border-radius:16px;margin-bottom:2rem;box-shadow:0 4px 12px rgba(0,0,0,0.1);">
        <h2 style="color:#6d4c41;margin-bottom:1rem;">Step 2: Check Status</h2>
        <button onclick="checkStatus()" style="background:#2196f3;color:white;border:none;padding:1rem 2rem;border-radius:8px;font-size:1.1rem;cursor:pointer;font-weight:600;">
            🔍 Check Files
        </button>
    </div>
    
    <div id="result" style="background:#f5f5f5;padding:2rem;border-radius:16px;min-height:200px;">
        <p style="color:#666;">Click buttons above to test...</p>
    </div>
</div>

<script>
function triggerFeeding() {
    const result = document.getElementById('result');
    result.innerHTML = '<p style="color:#ff9800;">⏳ Triggering feeding...</p>';
    
    const formData = new FormData();
    formData.append('duration', '10');
    formData.append('cage_number', '1');
    
    fetch('/feeder/manual/feed', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        result.innerHTML = `
            <h3 style="color:#4caf50;">✅ Feeding Triggered!</h3>
            <pre style="background:#fff;padding:1rem;border-radius:8px;overflow:auto;">${JSON.stringify(data, null, 2)}</pre>
            <p style="margin-top:1rem;"><strong>Now check:</strong></p>
            <ol style="line-height:2;">
                <li>Go to <a href="/feeder" target="_blank" style="color:#2196f3;">Feeder Page</a> - Animation should appear</li>
                <li>Go to <a href="/dashboard" target="_blank" style="color:#2196f3;">Dashboard</a> - Notification should appear</li>
            </ol>
        `;
        
        // Auto-check status after 2 seconds
        setTimeout(checkStatus, 2000);
    })
    .catch(err => {
        result.innerHTML = `<p style="color:#f44336;">❌ Error: ${err.message}</p>`;
    });
}

function checkStatus() {
    const result = document.getElementById('result');
    result.innerHTML = '<p style="color:#ff9800;">⏳ Checking files...</p>';
    
    Promise.all([
        fetch('/api/feeder/animation').then(r => r.json()),
        fetch('/api/feeder/feeding-notification').then(r => r.json())
    ])
    .then(([anim, notif]) => {
        const animActive = anim.active ? '✅ ACTIVE' : '❌ NOT ACTIVE';
        const notifActive = notif.active ? '✅ ACTIVE' : '❌ NOT ACTIVE';
        
        result.innerHTML = `
            <h3 style="color:#6d4c41;">📊 Status Check</h3>
            
            <div style="background:#fff;padding:1rem;border-radius:8px;margin:1rem 0;">
                <h4 style="color:#2196f3;">Animation File: ${animActive}</h4>
                <pre style="background:#f5f5f5;padding:1rem;border-radius:8px;overflow:auto;font-size:0.9rem;">${JSON.stringify(anim, null, 2)}</pre>
            </div>
            
            <div style="background:#fff;padding:1rem;border-radius:8px;margin:1rem 0;">
                <h4 style="color:#ff9800;">Notification File: ${notifActive}</h4>
                <pre style="background:#f5f5f5;padding:1rem;border-radius:8px;overflow:auto;font-size:0.9rem;">${JSON.stringify(notif, null, 2)}</pre>
            </div>
            
            ${anim.active ? '<p style="color:#4caf50;font-weight:600;">✅ Animation should be showing on feeder page!</p>' : '<p style="color:#f44336;font-weight:600;">❌ Animation file not active. Try triggering feeding again.</p>'}
            ${notif.active ? '<p style="color:#4caf50;font-weight:600;">✅ Notification should be showing on dashboard!</p>' : '<p style="color:#666;">ℹ️ Notification already shown or not triggered yet.</p>'}
        `;
    })
    .catch(err => {
        result.innerHTML = `<p style="color:#f44336;">❌ Error: ${err.message}</p>`;
    });
}

// Auto-check every 3 seconds
setInterval(checkStatus, 3000);
checkStatus();
</script>
@endsection

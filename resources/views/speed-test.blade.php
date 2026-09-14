@extends('layouts.fast-app')

@section('content')
<div style="padding: 2rem; max-width: 800px; margin: 0 auto;">
    <h1 style="color: #047857; margin-bottom: 2rem;">🚀 Navigation Speed Test</h1>
    
    <div style="background: white; padding: 1.5rem; border-radius: 12px; box-shadow: 0 4px 16px rgba(134, 239, 172, 0.1); margin-bottom: 2rem;">
        <h2 style="color: #1f2937; margin-bottom: 1rem;">Quick Navigation Links</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <a href="{{ route('feeder') }}" style="background: #86efac; color: white; padding: 1rem; border-radius: 8px; text-decoration: none; text-align: center; font-weight: 600;">🌾 Feeder</a>
            <a href="{{ route('inventory') }}" style="background: #86efac; color: white; padding: 1rem; border-radius: 8px; text-decoration: none; text-align: center; font-weight: 600;">📦 Inventory</a>
            <a href="{{ route('settings') }}" style="background: #86efac; color: white; padding: 1rem; border-radius: 8px; text-decoration: none; text-align: center; font-weight: 600;">⚙️ Settings</a>
        </div>
    </div>
    
    <div style="background: #f0fdf4; padding: 1.5rem; border-radius: 12px; border-left: 4px solid #86efac;">
        <h3 style="color: #047857; margin-bottom: 1rem;">Performance Tips:</h3>
        <ul style="color: #1f2937; line-height: 1.6;">
            <li>✅ Optimized CSS and JavaScript loaded</li>
            <li>✅ Database queries limited and cached</li>
            <li>✅ Reduced polling frequency</li>
            <li>✅ Minimal animations for faster rendering</li>
        </ul>
    </div>
    
    <div style="margin-top: 2rem; text-align: center;">
        <p style="color: #6b7280; font-size: 0.875rem;">
            Navigation should now load much faster. If still slow, check browser console (F12) for errors.
        </p>
    </div>
</div>

<script>
// Simple loading time measurement
window.addEventListener('load', function() {
    const loadTime = performance.now();
    console.log('Page loaded in:', Math.round(loadTime), 'ms');
    
    if (loadTime > 3000) {
        console.warn('Page took longer than 3 seconds to load. Check network and database.');
    }
});
</script>
@endsection
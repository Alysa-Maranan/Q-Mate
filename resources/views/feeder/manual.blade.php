@extends('layouts.app')

@section('content')
<h1 class="text-3xl font-bold mb-6 text-green-800">🚨 Manual Feed</h1>
<div class="bg-white p-6 rounded-lg shadow border border-green-200">
    <h2 class="text-xl font-semibold mb-4 text-green-700">Emergency Feed</h2>
    <p class="mb-4 text-green-600">Activate manual feeding for emergency situations.</p>
    
    <div id="status-message" class="mb-4 p-4 rounded hidden"></div>
    
    <form id="manual-feed-form">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-[#8d6e63]">Duration (seconds)</label>
            <select name="duration" id="duration" class="mt-1 block w-full px-3 py-2 border border-[#bcaaa4] rounded-md">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="30">30</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-[#8d6e63]">Cage Number</label>
            <select name="cage_number" id="cage_number" class="mt-1 block w-full px-3 py-2 border border-[#bcaaa4] rounded-md">
                <option value="1">Cage 1</option>
                <option value="2">Cage 2</option>
            </select>
        </div>
        <button type="submit" id="feed-btn" class="bg-red-600 text-white px-6 py-3 rounded hover:bg-red-700 text-lg font-semibold">
            Activate Feed
        </button>
    </form>
    <div class="mt-6 p-4 bg-yellow-100 border border-yellow-400 rounded">
        <p class="text-yellow-800">Warning: Manual feeding should only be used in emergencies. Overfeeding may harm the quail.</p>
    </div>
</div>

<script>
document.getElementById('manual-feed-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const btn = document.getElementById('feed-btn');
    const statusDiv = document.getElementById('status-message');
    const duration = document.getElementById('duration').value;
    const cage = document.getElementById('cage_number').value;
    
    btn.disabled = true;
    btn.textContent = 'Feeding...';
    
    statusDiv.className = 'mb-4 p-4 rounded bg-blue-100 border border-blue-400';
    statusDiv.textContent = `Starting manual feed for ${duration} seconds on Cage ${cage}...`;
    statusDiv.classList.remove('hidden');
    
    try {
        const response = await fetch('{{ route("feeder.manual.feed") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                duration: parseInt(duration),
                cage_number: parseInt(cage),
                force_serial: true
            })
        });
        
        const data = await response.json();
        
        if (data.status === 'feeding_now') {
            statusDiv.className = 'mb-4 p-4 rounded bg-green-100 border border-green-400';
            statusDiv.textContent = `✅ Feeding started! Servo will run for ${duration} seconds on Cage ${cage}.`;
            
            setTimeout(() => {
                statusDiv.className = 'mb-4 p-4 rounded bg-green-100 border border-green-400';
                statusDiv.textContent = '✅ Feeding completed!';
                btn.disabled = false;
                btn.textContent = 'Activate Feed';
            }, duration * 1000 + 2000);
        } else {
            throw new Error(data.message || 'Unknown error');
        }
    } catch (error) {
        statusDiv.className = 'mb-4 p-4 rounded bg-red-100 border border-red-400';
        statusDiv.textContent = '❌ Error: ' + error.message;
        btn.disabled = false;
        btn.textContent = 'Activate Feed';
    }
});
</script>
@endsection
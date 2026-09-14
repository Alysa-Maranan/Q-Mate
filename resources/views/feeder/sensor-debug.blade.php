@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold mb-6">🔧 Sensor Debug Center</h1>
        
        <!-- API Test Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-blue-50 p-6 rounded-lg border-2 border-blue-300">
                <h2 class="text-xl font-bold mb-4">🔍 Test API Endpoint</h2>
                <button 
                    onclick="testAPI()" 
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg transition">
                    Test /api/sensor-data/latest
                </button>
                <pre id="apiResponse" class="bg-white p-4 rounded mt-4 text-sm font-mono border-2 border-blue-200" style="max-height: 300px; overflow-y: auto;">
Waiting for response...
                </pre>
            </div>
            
            <div class="bg-green-50 p-6 rounded-lg border-2 border-green-300">
                <h2 class="text-xl font-bold mb-4">🌡️ Python Script Test</h2>
                <button 
                    onclick="testPython()" 
                    class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition">
                    Test servo_dht22_web.py
                </button>
                <pre id="pythonResponse" class="bg-white p-4 rounded mt-4 text-sm font-mono border-2 border-green-200" style="max-height: 300px; overflow-y: auto;">
Waiting for response...
                </pre>
            </div>
        </div>

        <!-- Database Status -->
        <div class="bg-gray-50 p-6 rounded-lg border-2 border-gray-300 mb-8">
            <h2 class="text-xl font-bold mb-4">📊 Database Status</h2>
            <div id="dbStatus" class="font-mono text-sm">
                Loading database info...
            </div>
        </div>

        <!-- Fetch Fresh Sensor Data -->
        <div class="bg-purple-50 p-6 rounded-lg border-2 border-purple-300">
            <h2 class="text-xl font-bold mb-4">🚀 Fetch Fresh Data</h2>
            <div class="flex gap-4 flex-wrap">
                <button 
                    onclick="fetchFreshSensor()" 
                    class="flex-1 min-w-[200px] bg-purple-500 hover:bg-purple-600 text-white font-bold py-3 px-6 rounded-lg transition">
                    Read Sensor Only
                </button>
                <button 
                    onclick="fetchFreshServo()" 
                    class="flex-1 min-w-[200px] bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-lg transition">
                    Trigger Servo + Read
                </button>
                <button 
                    onclick="checkDatabase()" 
                    class="flex-1 min-w-[200px] bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition">
                    Check DB
                </button>
            </div>
        </div>
    </div>
</div>

<script>
async function testAPI() {
    const box = document.getElementById('apiResponse');
    box.textContent = 'Loading...';
    
    try {
        const response = await fetch('/api/sensor-data/latest');
        const data = await response.json();
        box.textContent = JSON.stringify(data, null, 2);
    } catch (error) {
        box.textContent = 'ERROR: ' + error.message;
    }
}

async function testPython() {
    const box = document.getElementById('pythonResponse');
    box.textContent = 'Loading...';
    
    try {
        const response = await fetch('/feeder/read-sensors');
        const data = await response.json();
        box.textContent = JSON.stringify(data, null, 2);
    } catch (error) {
        box.textContent = 'ERROR: ' + error.message;
    }
}

async function fetchFreshSensor() {
    document.getElementById('pythonResponse').textContent = 'Fetching...';
    const response = await fetch('/feeder/read-sensors');
    const data = await response.json();
    document.getElementById('pythonResponse').textContent = JSON.stringify(data, null, 2);
    setTimeout(() => testAPI(), 1000);
}

async function fetchFreshServo() {
    document.getElementById('pythonResponse').textContent = 'Fetching...';
    const response = await fetch('/feeder/trigger-servo');
    const data = await response.json();
    document.getElementById('pythonResponse').textContent = JSON.stringify(data, null, 2);
    setTimeout(() => testAPI(), 2000);
}

async function checkDatabase() {
    const response = await fetch('/api/sensor-data/history?hours=1');
    const data = await response.json();
    
    const box = document.getElementById('dbStatus');
    let html = `
        <p><strong>Total readings in last hour:</strong> ${data.count}</p>
        <p><strong>Latest 5 readings:</strong></p>
        <table class="w-full border mt-2">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border p-2">Time</th>
                    <th class="border p-2">Temp (°C)</th>
                    <th class="border p-2">Humidity (%)</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    const readings = data.data.slice(-5).reverse();
    readings.forEach(r => {
        const time = new Date(r.recorded_at).toLocaleTimeString();
        html += `
            <tr>
                <td class="border p-2">${time}</td>
                <td class="border p-2 text-center">${r.temperature ? r.temperature.toFixed(1) : 'N/A'}</td>
                <td class="border p-2 text-center">${r.humidity ? r.humidity.toFixed(1) : 'N/A'}</td>
            </tr>
        `;
    });
    
    html += `
            </tbody>
        </table>
    `;
    
    box.innerHTML = html;
}

// Auto-check on load
window.addEventListener('load', () => {
    setTimeout(checkDatabase, 500);
});
</script>

<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 2rem 0;
    }
</style>
@endsection

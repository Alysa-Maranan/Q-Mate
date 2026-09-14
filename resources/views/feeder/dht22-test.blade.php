@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold mb-6">🌡️ WEMOS DHT22 Sensor Test</h1>
        
        <!-- Sensor Reading Display -->
        <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="bg-blue-50 p-6 rounded-lg border-2 border-blue-300">
                <div class="text-sm text-gray-600">Temperature</div>
                <div class="text-4xl font-bold text-blue-600" id="tempDisplay">--</div>
                <div class="text-xs text-gray-500">°C</div>
            </div>
            <div class="bg-green-50 p-6 rounded-lg border-2 border-green-300">
                <div class="text-sm text-gray-600">Humidity</div>
                <div class="text-4xl font-bold text-green-600" id="humidityDisplay">--</div>
                <div class="text-xs text-gray-500">%</div>
            </div>
        </div>

        <!-- Control Buttons -->
        <div class="flex gap-4 mb-8">
            <button 
                onclick="readSensors()" 
                class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg transition">
                📊 Read Sensors Only
            </button>
            <button 
                onclick="triggerServoAndRead()" 
                class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition">
                🍽️ Trigger Servo + Read
            </button>
        </div>

        <!-- Status Message -->
        <div id="statusBox" class="mb-6 p-4 rounded-lg border-2" style="display:none;">
            <div id="statusMessage"></div>
        </div>

        <!-- Response Details -->
        <div class="bg-gray-100 p-4 rounded-lg font-mono text-sm">
            <div class="font-bold mb-2">📋 Response Details:</div>
            <pre id="responseBox">Waiting for response...</pre>
        </div>

        <!-- History Table -->
        <div class="mt-8">
            <h2 class="text-2xl font-bold mb-4">📈 Recent Readings</h2>
            <div id="historyTable" class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="border p-2">Time</th>
                            <th class="border p-2">Temperature (°C)</th>
                            <th class="border p-2">Humidity (%)</th>
                        </tr>
                    </thead>
                    <tbody id="historyBody">
                        <tr><td colspan="3" class="border p-2 text-center text-gray-500">No readings yet</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
const API_BASE = '/feeder';
let history = [];

async function readSensors() {
    updateStatus('⏳ Reading sensors...', 'info');
    setResponseBox('Loading...');
    
    try {
        const response = await fetch(`${API_BASE}/read-sensors`);
        const data = await response.json();
        
        console.log('Response:', data);
        setResponseBox(JSON.stringify(data, null, 2));
        
        if (data.success && data.data) {
            updateDisplay(data.data.temperature, data.data.humidity);
            addToHistory(data.data.temperature, data.data.humidity);
            updateStatus(`✅ Sensors read successfully!`, 'success');
        } else {
            updateStatus(`❌ Error: ${data.message || 'Unknown error'}`, 'error');
        }
    } catch (error) {
        updateStatus(`❌ Connection error: ${error.message}`, 'error');
        setResponseBox(`Error: ${error.message}`);
    }
}

async function triggerServoAndRead() {
    updateStatus('⏳ Triggering servo and reading sensors...', 'info');
    setResponseBox('Loading...');
    
    try {
        const response = await fetch(`${API_BASE}/trigger-servo`);
        const data = await response.json();
        
        console.log('Response:', data);
        setResponseBox(JSON.stringify(data, null, 2));
        
        if (data.success && data.result && data.result.sensor && data.result.sensor.data) {
            updateDisplay(data.result.sensor.data.temperature, data.result.sensor.data.humidity);
            addToHistory(data.result.sensor.data.temperature, data.result.sensor.data.humidity);
            updateStatus('✅ Servo triggered! Sensors read successfully!', 'success');
        } else {
            updateStatus(`❌ Error: ${data.message || 'Unknown error'}`, 'error');
        }
    } catch (error) {
        updateStatus(`❌ Connection error: ${error.message}`, 'error');
        setResponseBox(`Error: ${error.message}`);
    }
}

function updateDisplay(temp, humidity) {
    if (temp !== null && temp !== undefined) {
        document.getElementById('tempDisplay').textContent = temp.toFixed(1);
    }
    if (humidity !== null && humidity !== undefined) {
        document.getElementById('humidityDisplay').textContent = humidity.toFixed(1);
    }
}

function updateStatus(message, type = 'info') {
    const box = document.getElementById('statusBox');
    const msg = document.getElementById('statusMessage');
    
    box.style.display = 'block';
    msg.textContent = message;
    
    // Change color based on type
    if (type === 'success') {
        box.className = 'mb-6 p-4 rounded-lg border-2 border-green-500 bg-green-50';
    } else if (type === 'error') {
        box.className = 'mb-6 p-4 rounded-lg border-2 border-red-500 bg-red-50';
    } else {
        box.className = 'mb-6 p-4 rounded-lg border-2 border-blue-500 bg-blue-50';
    }
}

function setResponseBox(content) {
    document.getElementById('responseBox').textContent = content;
}

function addToHistory(temp, humidity) {
    const now = new Date();
    const time = now.toLocaleTimeString();
    
    history.unshift({ time, temp, humidity });
    if (history.length > 10) history.pop(); // Keep last 10
    
    renderHistory();
}

function renderHistory() {
    const tbody = document.getElementById('historyBody');
    
    if (history.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="border p-2 text-center text-gray-500">No readings yet</td></tr>';
        return;
    }
    
    tbody.innerHTML = history.map(h => `
        <tr>
            <td class="border p-2">${h.time}</td>
            <td class="border p-2 text-center">${h.temp !== null ? h.temp.toFixed(1) : 'N/A'}</td>
            <td class="border p-2 text-center">${h.humidity !== null ? h.humidity.toFixed(1) : 'N/A'}</td>
        </tr>
    `).join('');
}

// Auto-refresh every 30 seconds
setInterval(readSensors, 30000);

// Initial message
setResponseBox('Click a button above to start testing the DHT22 sensor!');
</script>

<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 2rem 0;
    }
</style>
@endsection

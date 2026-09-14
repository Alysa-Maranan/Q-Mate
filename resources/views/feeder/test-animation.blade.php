<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animation Test - SQUIFM</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #1a1a2e;
            color: white;
        }
        .log {
            background: #16213e;
            border: 1px solid #0f3460;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            max-height: 400px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }
        .log-entry {
            margin: 5px 0;
            padding: 5px;
        }
        .success { color: #00ff00; }
        .error { color: #ff0000; }
        .info { color: #00bfff; }
        .warning { color: #ffa500; }
        button {
            background: #0f3460;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        button:hover {
            background: #16213e;
        }
        .status-box {
            background: #16213e;
            border: 2px solid #0f3460;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>🔍 SQUIFM Feeding Animation Test</h1>
    
    <div class="status-box">
        <h2>Current Status</h2>
        <p><strong>Status:</strong> <span id="current-status">Checking...</span></p>
        <p><strong>Schedule ID:</strong> <span id="schedule-id">-</span></p>
        <p><strong>Time:</strong> <span id="schedule-time">-</span></p>
        <p><strong>Amount:</strong> <span id="schedule-amount">-</span></p>
    </div>

    <div>
        <button onclick="checkStatus()">🔄 Check Status Now</button>
        <button onclick="triggerAutoCheck()">⚡ Trigger Auto-Check</button>
        <button onclick="clearLogs()">🗑️ Clear Logs</button>
        <button onclick="location.href='/feeder'">🏠 Back to Feeder</button>
    </div>

    <h3>📋 Activity Log:</h3>
    <div class="log" id="log"></div>

    <script>
        let logContainer = document.getElementById('log');
        let checkInterval;

        function log(message, type = 'info') {
            const timestamp = new Date().toLocaleTimeString();
            const entry = document.createElement('div');
            entry.className = `log-entry ${type}`;
            entry.textContent = `[${timestamp}] ${message}`;
            logContainer.insertBefore(entry, logContainer.firstChild);
        }

        function clearLogs() {
            logContainer.innerHTML = '';
            log('Logs cleared', 'info');
        }

        async function checkStatus() {
            try {
                log('Checking feeding status...', 'info');
                const response = await fetch('/feeder/schedules/status');
                const data = await response.json();
                
                document.getElementById('current-status').textContent = data.status;
                document.getElementById('schedule-id').textContent = data.schedule_id || '-';
                document.getElementById('schedule-time').textContent = data.time || '-';
                document.getElementById('schedule-amount').textContent = data.amount || '-';

                if (data.status === 'feeding_now') {
                    log('✅ FEEDING NOW! Schedule #' + data.schedule_id + ' | ' + data.amount + ' seconds', 'success');
                } else if (data.status === 'done_feeding') {
                    log('✅ Done feeding: Schedule #' + data.schedule_id, 'success');
                } else {
                    log('Status: ' + data.status, 'info');
                }
            } catch (error) {
                log('❌ Error checking status: ' + error.message, 'error');
            }
        }

        async function triggerAutoCheck() {
            try {
                log('⚡ Triggering auto-check...', 'warning');
                const response = await fetch('/feeder/schedules/auto-check');
                const data = await response.json();
                
                log('Auto-check completed at: ' + data.checked_at, 'info');
                log('Triggered count: ' + data.triggered_count, data.triggered_count > 0 ? 'success' : 'info');
                
                if (data.triggered_count > 0) {
                    data.triggered_schedules.forEach(schedule => {
                        log('🚀 Triggered Schedule #' + schedule.id + ' at ' + schedule.time + ' (' + schedule.amount + 's)', 'success');
                    });
                    
                    // Check status immediately multiple times
                    log('Checking status in rapid succession...', 'warning');
                    setTimeout(() => checkStatus(), 100);
                    setTimeout(() => checkStatus(), 500);
                    setTimeout(() => checkStatus(), 1000);
                    setTimeout(() => checkStatus(), 2000);
                    setTimeout(() => checkStatus(), 3000);
                } else {
                    log('No schedules matched current time', 'warning');
                }
            } catch (error) {
                log('❌ Error in auto-check: ' + error.message, 'error');
            }
        }

        // Start monitoring
        log('🎯 Animation tester started', 'success');
        log('This page monitors feeding status in real-time', 'info');
        log('Click "Trigger Auto-Check" to manually check schedules', 'info');
        
        // Auto-check status every 2 seconds
        checkStatus();
        checkInterval = setInterval(checkStatus, 2000);

        // Stop monitoring when page unloads
        window.addEventListener('beforeunload', () => {
            clearInterval(checkInterval);
        });
    </script>
</body>
</html>

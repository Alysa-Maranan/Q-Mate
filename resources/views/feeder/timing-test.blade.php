<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Schedule Trigger Timing Test</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            margin: 0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .timeline {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            max-height: 400px;
            overflow-y: auto;
        }
        .event {
            padding: 8px 12px;
            margin: 5px 0;
            border-radius: 5px;
            display: flex;
            align-items: center;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from {
                transform: translateX(-20px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        .event-time {
            font-weight: bold;
            margin-right: 15px;
            color: #ffd700;
        }
        .event.trigger { background: rgba(255, 215, 0, 0.2); border-left: 4px solid #ffd700; }
        .event.status { background: rgba(0, 255, 255, 0.2); border-left: 4px solid #00ffff; }
        .event.servo { background: rgba(0, 255, 127, 0.2); border-left: 4px solid #00ff7f; }
        .event.animation { background: rgba(255, 0, 255, 0.2); border-left: 4px solid #ff00ff; }
        .event.error { background: rgba(255, 0, 0, 0.2); border-left: 4px solid #ff0000; }
        .controls {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        button {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        .btn-trigger {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-clear {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .btn-back {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        .timing-summary {
            background: rgba(0, 0, 0, 0.4);
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .timing-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            margin: 5px 0;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 5px;
        }
        .timing-value {
            font-weight: bold;
            color: #00ff7f;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>⏱️ Schedule Trigger Timing Test</h1>
        
        <div class="timing-summary">
            <h3>📊 Performance Metrics</h3>
            <div class="timing-item">
                <span>API Response Time:</span>
                <span class="timing-value" id="apiTime">-</span>
            </div>
            <div class="timing-item">
                <span>Animation Start Delay:</span>
                <span class="timing-value" id="animDelay">-</span>
            </div>
            <div class="timing-item">
                <span>Status Detection Time:</span>
                <span class="timing-value" id="statusTime">-</span>
            </div>
        </div>

        <div class="controls">
            <button class="btn-trigger" onclick="triggerSchedule()">🚀 Trigger Schedule Now</button>
            <button class="btn-clear" onclick="clearTimeline()">🗑️ Clear Log</button>
            <button class="btn-back" onclick="location.href='/feeder'">🏠 Back to Feeder</button>
        </div>

        <h3>📝 Event Timeline (Real-time)</h3>
        <div class="timeline" id="timeline"></div>
    </div>

    <script>
        let startTime = null;
        let apiResponseTime = null;

        function addEvent(message, type = 'status') {
            const timeline = document.getElementById('timeline');
            const event = document.createElement('div');
            event.className = `event ${type}`;
            
            const now = new Date();
            const timeStr = now.toLocaleTimeString() + '.' + now.getMilliseconds().toString().padStart(3, '0');
            const elapsed = startTime ? ((now - startTime) / 1000).toFixed(3) + 's' : '0.000s';
            
            event.innerHTML = `
                <span class="event-time">${timeStr}</span>
                <span>+${elapsed}</span>
                <span style="margin-left: 15px;">${message}</span>
            `;
            
            timeline.insertBefore(event, timeline.firstChild);
        }

        function clearTimeline() {
            document.getElementById('timeline').innerHTML = '';
            document.getElementById('apiTime').textContent = '-';
            document.getElementById('animDelay').textContent = '-';
            document.getElementById('statusTime').textContent = '-';
            startTime = null;
            apiResponseTime = null;
            addEvent('Timeline cleared', 'status');
        }

        async function triggerSchedule() {
            startTime = new Date();
            addEvent('🎯 Triggering auto-check API...', 'trigger');

            try {
                const response = await fetch('/feeder/schedules/auto-check');
                apiResponseTime = ((new Date() - startTime) / 1000).toFixed(3);
                
                document.getElementById('apiTime').textContent = apiResponseTime + 's';
                addEvent(`✅ API responded in ${apiResponseTime}s`, 'status');
                
                const data = await response.json();
                
                if (data.triggered_count > 0) {
                    addEvent(`🚀 ${data.triggered_count} schedule(s) triggered!`, 'trigger');
                    
                    data.triggered_schedules.forEach(schedule => {
                        addEvent(`📅 Schedule #${schedule.id} at ${schedule.time} (${schedule.amount}s)`, 'servo');
                    });
                    
                    // This should show animation IMMEDIATELY
                    const animStart = ((new Date() - startTime) / 1000).toFixed(3);
                    document.getElementById('animDelay').textContent = animStart + 's';
                    addEvent(`🎬 Animation triggered (${animStart}s from start)`, 'animation');
                    
                    // Now check status multiple times
                    checkStatusMultiple();
                } else {
                    addEvent('⚠️ No schedules matched current time', 'error');
                }
            } catch (error) {
                addEvent(`❌ Error: ${error.message}`, 'error');
            }
        }

        async function checkStatusMultiple() {
            const delays = [0, 500, 1000, 1500, 2000];
            
            for (const delay of delays) {
                setTimeout(async () => {
                    try {
                        const response = await fetch('/feeder/schedules/status');
                        const data = await response.json();
                        const elapsed = ((new Date() - startTime) / 1000).toFixed(3);
                        
                        if (data.status === 'feeding_now') {
                            document.getElementById('statusTime').textContent = elapsed + 's';
                            addEvent(`🟢 Status: FEEDING_NOW detected! (${elapsed}s)`, 'status');
                        } else if (data.status === 'done_feeding') {
                            addEvent(`🟡 Status: DONE_FEEDING (${elapsed}s)`, 'status');
                        } else {
                            addEvent(`⚪ Status: ${data.status} (${elapsed}s)`, 'status');
                        }
                    } catch (error) {
                        addEvent(`❌ Status check error: ${error.message}`, 'error');
                    }
                }, delay);
            }
        }

        // Initial log entry
        addEvent('Timing tester ready! Click "Trigger Schedule Now" to test', 'status');
        addEvent('Expected: API returns quickly (< 100ms), animation starts immediately', 'animation');
    </script>
</body>
</html>

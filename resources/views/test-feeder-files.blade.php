<!DOCTYPE html>
<html>
<head>
    <title>Test Animation & Notification</title>
</head>
<body>
    <h1>Test Feeder Animation & Dashboard Notification</h1>
    
    <button onclick="testAnimation()">Test Animation</button>
    <button onclick="testNotification()">Test Dashboard Notification</button>
    <button onclick="checkFiles()">Check Files</button>
    
    <div id="result" style="margin-top:20px;padding:20px;background:#f0f0f0;"></div>
    
    <script>
        function testAnimation() {
            fetch('/api/feeder/animation')
                .then(r => r.json())
                .then(data => {
                    document.getElementById('result').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                })
                .catch(err => {
                    document.getElementById('result').innerHTML = 'Error: ' + err.message;
                });
        }
        
        function testNotification() {
            fetch('/api/feeder/feeding-notification')
                .then(r => r.json())
                .then(data => {
                    document.getElementById('result').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                })
                .catch(err => {
                    document.getElementById('result').innerHTML = 'Error: ' + err.message;
                });
        }
        
        function checkFiles() {
            Promise.all([
                fetch('/api/feeder/animation').then(r => r.json()),
                fetch('/api/feeder/feeding-notification').then(r => r.json())
            ])
            .then(([anim, notif]) => {
                document.getElementById('result').innerHTML = 
                    '<h3>Animation File:</h3><pre>' + JSON.stringify(anim, null, 2) + '</pre>' +
                    '<h3>Notification File:</h3><pre>' + JSON.stringify(notif, null, 2) + '</pre>';
            });
        }
        
        // Auto-check every 2 seconds
        setInterval(checkFiles, 2000);
        checkFiles();
    </script>
</body>
</html>

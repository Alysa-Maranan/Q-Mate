<!DOCTYPE html>
<html>
<head>
    <title>Analytics API Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Analytics API Test</h1>
    <button onclick="testAnalytics()">Test Analytics API</button>
    <div id="result"></div>

    <script>
        function testAnalytics() {
            const fromDate = '{{ date("Y-m-d", strtotime("-30 days")) }}';
            const toDate = '{{ date("Y-m-d") }}';
            
            fetch(`/api/analytics?from_date=${fromDate}&to_date=${toDate}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('result').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
            })
            .catch(error => {
                document.getElementById('result').innerHTML = '<p style="color: red;">Error: ' + error.message + '</p>';
            });
        }
    </script>
</body>
</html>
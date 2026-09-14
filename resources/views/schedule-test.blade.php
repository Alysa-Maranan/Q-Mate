<!DOCTYPE html>
<html>
<head>
    <title>Schedule Test</title>
</head>
<body>
    <h1>Add Schedule Test</h1>
    
    @if(session('success'))
        <div style="color: green; padding: 10px; border: 1px solid green;">
            SUCCESS: {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div style="color: red; padding: 10px; border: 1px solid red;">
            ERROR: {{ session('error') }}
        </div>
    @endif
    
    @if($errors->any())
        <div style="color: red; padding: 10px; border: 1px solid red;">
            VALIDATION ERRORS:
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form method="POST" action="{{ url('/feeder/schedules') }}" style="padding: 20px; border: 1px solid #ccc;">
        @csrf
        <div style="margin-bottom: 15px;">
            <label>Time:</label><br>
            <input type="time" name="time" value="{{ old('time', '12:00') }}" required>
        </div>
        
        <div style="margin-bottom: 15px;">
            <label>Duration (seconds):</label><br>
            <input type="number" name="amount" value="{{ old('amount', 5) }}" min="1" max="60" required>
        </div>
        
        <div style="margin-bottom: 15px;">
            <label>Days (optional):</label><br>
            <input type="checkbox" name="days[]" value="mon"> Monday<br>
            <input type="checkbox" name="days[]" value="tue"> Tuesday<br>
            <input type="checkbox" name="days[]" value="wed"> Wednesday<br>
            <input type="checkbox" name="days[]" value="thu"> Thursday<br>
            <input type="checkbox" name="days[]" value="fri"> Friday<br>
            <input type="checkbox" name="days[]" value="sat"> Saturday<br>
            <input type="checkbox" name="days[]" value="sun"> Sunday<br>
        </div>
        
        <button type="submit" style="padding: 10px 20px; background: #007cba; color: white; border: none;">
            Add Schedule
        </button>
    </form>
    
    <h2>Current Schedules:</h2>
    @php
        $schedules = \App\Models\FeedingSchedule::all();
    @endphp
    
    @if($schedules->count() > 0)
        <table border="1" style="width: 100%; margin-top: 20px;">
            <tr>
                <th>ID</th>
                <th>Time</th>
                <th>Amount</th>
                <th>Days</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
            @foreach($schedules as $schedule)
            <tr>
                <td>{{ $schedule->id }}</td>
                <td>{{ $schedule->time }}</td>
                <td>{{ $schedule->amount }}</td>
                <td>{{ is_array($schedule->days) ? implode(', ', $schedule->days) : $schedule->days }}</td>
                <td>{{ $schedule->status ?? 'idle' }}</td>
                <td>{{ $schedule->created_at }}</td>
            </tr>
            @endforeach
        </table>
    @else
        <p>No schedules found.</p>
    @endif
</body>
</html>
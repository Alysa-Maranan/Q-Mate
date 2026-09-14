@extends('layouts.app')

@section('content')
<h1 class="text-3xl font-bold mb-6 text-[#6d4c41]">⏱️ Feeding Schedule</h1>
<div class="bg-white p-6 rounded-lg shadow mb-6 border border-[#a1887f]">
    <h2 class="text-xl font-semibold mb-4 text-[#8d6e63]">Add New Schedule</h2>
    <form method="POST" action="{{ url('/feeder/schedules') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-green-700">Time</label>
                <input type="time" name="time" class="mt-1 block w-full px-3 py-2 border border-[#bcaaa4] rounded-md" required value="{{ old('time') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-green-700">Days</label>
                <div class="mt-1 flex space-x-2">
                    <label><input type="checkbox" name="days[]" value="mon" {{ is_array(old('days')) && in_array('mon', old('days')) ? 'checked' : '' }}> Mon</label>
                    <label><input type="checkbox" name="days[]" value="tue" {{ is_array(old('days')) && in_array('tue', old('days')) ? 'checked' : '' }}> Tue</label>
                    <label><input type="checkbox" name="days[]" value="wed" {{ is_array(old('days')) && in_array('wed', old('days')) ? 'checked' : '' }}> Wed</label>
                    <label><input type="checkbox" name="days[]" value="thu" {{ is_array(old('days')) && in_array('thu', old('days')) ? 'checked' : '' }}> Thu</label>
                    <label><input type="checkbox" name="days[]" value="fri" {{ is_array(old('days')) && in_array('fri', old('days')) ? 'checked' : '' }}> Fri</label>
                    <label><input type="checkbox" name="days[]" value="sat" {{ is_array(old('days')) && in_array('sat', old('days')) ? 'checked' : '' }}> Sat</label>
                    <label><input type="checkbox" name="days[]" value="sun" {{ is_array(old('days')) && in_array('sun', old('days')) ? 'checked' : '' }}> Sun</label>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <label class="block text-sm font-medium text-[#8d6e63]">Duration (seconds)</label>
            <input type="number" name="amount" min="1" value="{{ old('amount', 1) }}" class="mt-1 block w-32 px-3 py-2 border border-[#bcaaa4] rounded-md">
        </div>
        <button type="submit" class="mt-4 add-schedule-btn text-white px-4 py-2 rounded" style="background:#a1887f;font-size:1.1rem;font-weight:600;letter-spacing:0.5px;box-shadow:0 2px 8px rgba(141,110,99,0.10);" onmouseover="this.style.background='#8d6e63'" onmouseout="this.style.background='#a1887f'">➕ Add Schedule</button>
    </form>
</div>
<div class="bg-white p-6 rounded-lg shadow border border-[#a1887f]">
    <h2 class="text-xl font-semibold mb-4 text-[#8d6e63]">Current Schedules</h2>
    <table class="w-full table-auto">
        <thead>
            <tr class="border-b border-green-200">
                <th class="text-left py-2 text-green-700">Time</th>
                <th class="text-left py-2 text-green-700">Days</th>
                <th class="text-left py-2 text-green-700">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($schedules as $s)
            <tr class="border-b border-[#a1887f]">
                <td class="py-2 text-green-800">{{ 
                    \Carbon\Carbon::createFromFormat('H:i:s', $s->time)->format('h:i A')
                }}</td>
                <td class="py-2 text-[#6d4c41]">{{ empty($s->days) ? 'Every Day' : implode(', ', $s->days) }}</td>
                <td class="py-2">
                    <form method="POST" action="{{ url('/feeder/schedules/'.$s->id) }}" onsubmit="return confirm('Are you sure you want to delete this schedule?');" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 ml-2">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
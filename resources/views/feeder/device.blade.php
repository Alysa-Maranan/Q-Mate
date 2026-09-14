@extends('layouts.app')

@section('content')
<h1 class="text-3xl font-bold mb-6 text-green-800">📱 Device Status</h1>
<div class="bg-white p-6 rounded-lg shadow border border-green-200">
    <h2 class="text-xl font-semibold mb-4 text-green-700">ESP32 Monitoring</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="font-medium mb-2 text-green-700">Connection Status</h3>
            <div class="flex items-center">
                <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                <span class="text-green-600 font-semibold">Online</span>
            </div>
            <p class="text-sm text-green-600 mt-1">Last seen: 1 minute ago</p>
        </div>
        <div>
            <h3 class="font-medium mb-2 text-green-700">System Info</h3>
            <p class="text-green-800">Firmware Version: 1.2.3</p>
            <p class="text-green-800">IP Address: 192.168.1.100</p>
            <p class="text-green-800">Uptime: 5 days, 3 hours</p>
        </div>
    </div>
    <div class="mt-6">
        <h3 class="font-medium mb-2 text-green-700">Recent Events</h3>
        <ul class="list-disc list-inside space-y-1 text-green-800">
            <li>Device connected at 2024-02-06 09:00:00</li>
            <li>Feed level updated: 85%</li>
            <li>Scheduled feed executed successfully</li>
        </ul>
    </div>
</div>
@endsection
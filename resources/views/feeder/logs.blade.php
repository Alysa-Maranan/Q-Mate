@extends('layouts.app')

@section('content')
<h1 class="text-3xl font-bold mb-6 text-green-800">📋 Feeding Logs</h1>
<div class="bg-white p-6 rounded-lg shadow border border-green-200">
    <h2 class="text-xl font-semibold mb-4 text-green-700">Complete History</h2>
    <table class="w-full table-auto">
        <thead>
            <tr class="border-b border-green-200">
                <th class="text-left py-2 text-green-700">Date & Time</th>
                <th class="text-left py-2 text-green-700">Type</th>
                <th class="text-left py-2 text-green-700">Duration</th>
                <th class="text-left py-2 text-green-700">Status</th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b border-[#a1887f]">
                <td class="py-2 text-[#6d4c41]">2024-02-06 08:00:00</td>
                <td class="py-2 text-green-800">Scheduled</td>
                <td class="py-2 text-green-800">10 sec</td>
                <td class="py-2 text-green-600">Success</td>
            </tr>
            <tr class="border-b border-green-200">
                <td class="py-2 text-green-800">2024-02-06 12:00:00</td>
                <td class="py-2 text-[#6d4c41]">Scheduled</td>
                <td class="py-2 text-[#6d4c41]">10 sec</td>
                <td class="py-2 text-[#8d6e63]">Success</td>
            </tr>
            <tr class="border-b border-green-200">
                <td class="py-2 text-green-800">2024-02-05 18:00:00</td>
                <td class="py-2 text-green-800">Manual</td>
                <td class="py-2 text-green-800">5 sec</td>
                <td class="py-2 text-green-600">Success</td>
            </tr>
            <tr class="border-b border-green-200">
                <td class="py-2 text-green-800">2024-02-05 08:00:00</td>
                <td class="py-2 text-green-800">Scheduled</td>
                <td class="py-2 text-green-800">10 sec</td>
                <td class="py-2 text-red-600">Failed</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
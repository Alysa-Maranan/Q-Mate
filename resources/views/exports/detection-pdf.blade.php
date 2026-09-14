<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detection History Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #6d4c41;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #6d4c41;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #999;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-bottom: 30px;
        }
        .stat-box {
            background: #f5f0eb;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #d7ccc8;
        }
        .stat-label {
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #6d4c41;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table thead {
            background: #a1887f;
            color: white;
        }
        table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        table td {
            padding: 10px 12px;
            border-bottom: 1px solid #efebe9;
        }
        table tbody tr:nth-child(even) {
            background: #f5f0eb;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #d7ccc8;
            padding-top: 15px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-quail {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .badge-human {
            background: #fff3e0;
            color: #e65100;
        }
        .badge-unknown {
            background: #f5f5f5;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔍 Detection History Report</h1>
        <p>Generated on {{ date('F d, Y H:i:s') }}</p>
        <p>Period: {{ $dateFrom }} to {{ $dateTo }}</p>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="stat-label">Total</div>
            <div class="stat-value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Quails</div>
            <div class="stat-value">{{ $stats['quail'] }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Humans</div>
            <div class="stat-value">{{ $stats['human'] }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Unknown</div>
            <div class="stat-value">{{ $stats['unknown'] }}</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Avg Confidence</div>
            <div class="stat-value">{{ $stats['avgConfidence'] ? round($stats['avgConfidence'] * 100) : 0 }}%</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date & Time</th>
                <th>Type</th>
                <th>Breed</th>
                <th>Confidence</th>
                <th>Location</th>
            </tr>
        </thead>
        <tbody>
            @forelse($detections as $detection)
                <tr>
                    <td>{{ $detection->created_at->format('M d, Y H:i') }}</td>
                    <td>
                        @if($detection->detection_type === 'quail')
                            <span class="badge badge-quail">✓ QUAIL</span>
                        @elseif($detection->detection_type === 'human')
                            <span class="badge badge-human">👤 HUMAN</span>
                        @else
                            <span class="badge badge-unknown">❓ UNKNOWN</span>
                        @endif
                    </td>
                    <td>{{ $detection->breed?->name ?? 'N/A' }}</td>
                    <td>{{ $detection->confidence ? round($detection->confidence * 100) . '%' : 'N/A' }}</td>
                    <td>{{ $detection->location ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">No detections found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This report was automatically generated from Escalona's Quail Farm Detection System</p>
    </div>
</body>
</html>

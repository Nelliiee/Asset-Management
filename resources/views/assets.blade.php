<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assets</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 32px 20px;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .brand {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .nav a {
            text-decoration: none;
            color: #1e40af;
            margin-left: 14px;
            font-weight: 700;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            color: #475569;
        }
        .badge {
            display: inline-block;
            background: #dbeafe;
            color: #1d4ed8;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 0.85rem;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="topbar">
            <div class="brand">Asset Management</div>
            <div class="nav">
                <a href="/home">Home</a>
                <a href="/assets">Assets</a>
            </div>
        </div>

        <div class="card">
            <h1>Assets</h1>

            @if ($assets->isEmpty())
                <p>No assets found.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Asset Code</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Category</th>
                            <th>Brand</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assets as $asset)
                            <tr>
                                <td>{{ $asset->asset_code }}</td>
                                <td>{{ $asset->name }}</td>
                                <td><span class="badge">{{ ucfirst($asset->status) }}</span></td>
                                <td>{{ $asset->category?->name ?? 'Uncategorized' }}</td>
                                <td>{{ $asset->brand?->name ?? 'No brand' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</body>
</html>

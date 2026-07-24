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
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }
        .brand {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .nav {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .nav a {
            text-decoration: none;
            color: #1e40af;
            font-weight: 700;
            padding: 10px 14px;
            border-radius: 10px;
            background: #e0ecff;
        }
        @media (max-width: 640px) {
            .topbar {
                align-items: stretch;
            }
            .nav {
                width: 100%;
            }
            .nav a {
                flex: 1 1 100%;
                text-align: center;
            }
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
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.95rem;
            border: 0;
            cursor: pointer;
        }
        .btn-primary {
            background: #2563eb;
            color: white;
        }
        .btn-secondary {
            background: #e2e8f0;
            color: #0f172a;
        }
        .btn-danger {
            background: #fee2e2;
            color: #b91c1c;
        }
        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        @media (max-width: 640px) {
            .table-header {
                align-items: stretch;
            }
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="topbar">
            <div class="brand">Asset Management</div>
            <div class="nav">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('assets') }}">Assets</a>
            </div>
        </div>

        <livewire:assets-table />
    </div>
</body>
</html>

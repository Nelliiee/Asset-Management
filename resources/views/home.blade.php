<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Management Home</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }
        .container {
            max-width: 1120px;
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
        .hero {
            background: linear-gradient(135deg, #0f172a, #2563eb);
            color: white;
            padding: 32px;
            border-radius: 18px;
            margin-bottom: 24px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        }
        .card h3 {
            margin: 0 0 10px;
            color: #64748b;
            font-size: 0.9rem;
            text-transform: uppercase;
        }
        .card p {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 700;
        }
        .table-card {
            background: white;
            padding: 20px;
            border-radius: 14px;
            margin-top: 18px;
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
                <a href="/">Welcome</a>
                <a href="/home">Home</a>
            </div>
        </div>

        <section class="hero">
            <h1>Asset Dashboard</h1>
            <p>Track all company assets, manage inventory status, and stay on top of available resources.</p>
        </section>

        <section class="stats">
            <div class="card">
                <h3>Total Assets</h3>
                <p>248</p>
            </div>
            <div class="card">
                <h3>Assigned</h3>
                <p>142</p>
            </div>
            <div class="card">
                <h3>Maintenance</h3>
                <p>19</p>
            </div>
            <div class="card">
                <h3>Available</h3>
                <p>87</p>
            </div>
        </section>

        <section class="table-card">
            <h2>Recent Assets</h2>
            <table>
                <thead>
                    <tr>
                        <th>Asset Code</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Category</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>AST-001</td>
                        <td>Desktop PC</td>
                        <td><span class="badge">Assigned</span></td>
                        <td>Computers</td>
                    </tr>
                    <tr>
                        <td>AST-002</td>
                        <td>Projector</td>
                        <td><span class="badge">Available</span></td>
                        <td>Office Equipment</td>
                    </tr>
                    <tr>
                        <td>AST-003</td>
                        <td>Printer</td>
                        <td><span class="badge">Maintenance</span></td>
                        <td>Printing</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>

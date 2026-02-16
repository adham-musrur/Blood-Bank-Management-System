<?php
session_start();
include('db_connection.php');

// Handle search by Stock ID if passed via GET
$search_id = null;
if (isset($_GET['stock_id'])) {
    $stock_id = strtoupper(trim($_GET['stock_id']));
    $numeric_id = intval(substr($stock_id, 2)); // from ST01 → 1
    $stmt = $conn->prepare("
        SELECT bd.*, bg.BloodGroup 
        FROM bloodstock bd
        LEFT JOIN bloodgroup bg ON bd.BloodGroupID = bg.BloodGroupID
        WHERE bd.StockID = ?
    ");
    $stmt->bind_param("i", $numeric_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // All records
    $query = "
        SELECT bd.*, bg.BloodGroup
        FROM bloodstock bd
        LEFT JOIN bloodgroup bg ON bd.BloodGroupID = bg.BloodGroupID
    ";
    $result = $conn->query($query);
}

if (!$result) {
    die("SQL Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Blood Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #fef3f2;
        }

        .header-box {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-box img {
            height: 90px;
            margin-right: 20px;
        }

        .header-box h2 {
            font-weight: bold;
            color: #8B0000;
            margin: 0;
        }

        .table thead {
            background-color: #811700 ;
            color: white;
        }

        thead.table-new th {
            background-color: #8B0000 !important;
            color: white;
        }

        .search-input {
            width: 0;
            transition: width 0.4s ease;
            box-shadow: none;
        }

        .search-input:focus {
            width: 250px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }

        .search-form:hover .search-input {
            width: 250px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }

        .print-btn {
            float: right;
        }
    </style>
</head>
<body class="p-4">
<div class="container">
    <div class="header-box">
        <div class="d-flex align-items-center">
            <img src="Project logo.png" alt="Logo">
            <h2>Blood Stock Records</h2>
        </div>
        <form method="get" class="search-form no-print">
        <input type="text" name="stock_id" class="form-control search-input" placeholder="Search Stock ID (e.g., ST01)">
    </form>
    </div>

    <?php if ($result->num_rows == 0): ?>
        <div class="alert alert-warning">No stock records found.</div>
    <?php else: ?>
        <table class="table table-bordered table-hover">
            <thead class="table-new">
                <tr>
                    <th>Stock ID</th>
                    <th>BloodGroup ID</th>
                    <th>Quantity (ml)</th>
                    <th>Collection Date</th>
                    <th>Expiry Date</th>
                    <th>Cost</th>
                    <th>Last Update</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= 'ST' . str_pad($row['StockID'], 2, '0', STR_PAD_LEFT) ?></td>
                        <td>
                            <a href="blood_group.php?id=<?= urlencode($row['BloodGroupID']) ?>" target="_blank">
                                <?= 'BG' . str_pad($row['BloodGroupID'], 2, '0', STR_PAD_LEFT) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($row['Quantity']) ?></td>
                        <td><?= htmlspecialchars($row['CollectionDate']) ?></td>
                        <td><?= htmlspecialchars($row['ExpiryDate']) ?></td>
                        <td><?= htmlspecialchars($row['Cost']) ?></td>
                        <td><?= htmlspecialchars($row['LastUpdate']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="d-flex justify-content-between">
        <a href="dashboard.php" class="btn btn-secondary mb-3">← Back</a>
        <button onclick="window.print()" class="btn btn-outline-primary mb-3 print-btn">🖨️ Print</button>
    </div>
</div>
</body>
</html>

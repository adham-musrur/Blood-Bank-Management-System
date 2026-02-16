<?php
session_start();
include('db_connection.php');

$query = "
    SELECT bd.*, d.Name AS DonorName, s.Name AS StaffName
    FROM blooddonation bd
    LEFT JOIN donor d ON bd.DonorID = d.DonorID
    LEFT JOIN staff s ON bd.StaffID = s.StaffID
";
// Search logic
$search_id = isset($_GET['donation_id']) ? strtoupper(trim($_GET['donation_id'])) : '';

if (!empty($search_id) && preg_match('/^DA\d+$/', $search_id)) {
    $donation_numeric_id = intval(substr($search_id, 2)); // extract numeric part after 'DA'

    $stmt = $conn->prepare("
        SELECT bd.*, d.Name AS DonorName, s.Name AS StaffName
        FROM blooddonation bd
        LEFT JOIN donor d ON bd.DonorID = d.DonorID
        LEFT JOIN staff s ON bd.StaffID = s.StaffID
        WHERE bd.DonationID = ?
    ");
    $stmt->bind_param("i", $donation_numeric_id);
    $stmt->execute();
    $result = $stmt->get_result();

} else {
    // If no valid search or empty, show all
    $sql = "
        SELECT bd.*, d.Name AS DonorName, s.Name AS StaffName
        FROM blooddonation bd
        LEFT JOIN donor d ON bd.DonorID = d.DonorID
        LEFT JOIN staff s ON bd.StaffID = s.StaffID
        ORDER BY bd.DonationID DESC
    ";
    $result = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Blood Donation Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #fff3f0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: linear-gradient(to right, #9c0b0e, #640000);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .navbar .nav-link {
            color: #ffffff !important;
            font-weight: 500;
            margin-left: 10px;
            transition: background 0.3s, color 0.3s;
            border-radius: 8px;
            padding: 6px 14px;
        }

        .navbar .nav-link:hover {
            background-color: #ffffff;
            color: #9c0b0e !important;
        }

        .logo-title {
            display: flex;
            align-items: center;
            color: white;
        }

        .logo-title img {
            height: 55px;
            margin-right: 10px;
        }

        .page-header {
            background-color: #fff;
            margin-top: 90px;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 0 12px rgba(0,0,0,0.08);
            text-align: center;
        }

        .page-header h2 {
            font-weight: bold;
            color: #640000;
        }

        .search-form input {
            width: 0;
            transition: width 0.4s ease;
        }

        .search-form:hover input,
        .search-form input:focus {
            width: 220px;
            box-shadow: 0 0 6px rgba(0,0,0,0.2);
        }

        table {
            margin-top: 30px;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
        }

        thead.table-dark th {
            background-color: #640000 !important;
        }

        tbody tr:nth-child(even) {
            background-color: #fdf2f0;
        }

        tbody tr:hover {
            background-color: #ffe8e3;
        }

        .btn-secondary {
            background-color: #9c0b0e;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #640000;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid px-4">
        <div class="logo-title">
            <img src="Project logo.png" alt="Blood Bank Logo">
            <span class="fs-4 fw-bold">Blood Donation Records</span>
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
            <span class="navbar-toggler-icon bg-light"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="donor_list.php">Donor</a></li>
                <li class="nav-item"><a class="nav-link" href="blood_group.php">Blood Group</a></li>
                <li class="nav-item"><a class="nav-link" href="blood_stock.php">Blood Stock</a></li>
                <li class="nav-item"><a class="nav-link" href="staff_list.php">Staff </a></li>
                <li class="nav-item"><a class="nav-link text-warning" href="logout.php">Logout</a></li>
                <li class="nav-item ms-3">
                    <form method="get" class="search-form">
                        <input type="text" name="donation_id" class="form-control form-control-sm" placeholder="Search Donation ID">
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Page Header -->
<div class="container page-header">
    <h2>Blood Donation Details</h2>
    <p class="text-muted">Browse all donation entries and related information.</p>
</div>

<!-- Table Section -->
<div class="container">
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Donation ID</th>
                <th>Donor ID</th>
                <th>Staff ID</th>
                <th>Donation Date</th>
                <th>Volume (ml)</th>
                <th>Health Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= 'DA' . str_pad($row['DonationID'], 2, '0', STR_PAD_LEFT) ?></td>
                    <td>
                        <a href="donor_list.php?id=<?= urlencode($row['DonorID']) ?>" target="_blank">
                            <?= 'D' . str_pad($row['DonorID'], 2, '0', STR_PAD_LEFT) ?>
                        </a>
                    </td>
                    <td>
                        <a href="staff_list.php?id=<?= urlencode($row['StaffID']) ?>" target="_blank">
                            <?= 'S' . str_pad($row['StaffID'], 3, '0', STR_PAD_LEFT) ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($row['DonationDate']) ?></td>
                    <td><?= htmlspecialchars($row['Volume']) ?></td>
                    <td><?= htmlspecialchars($row['HealthStatus']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-secondary mt-3">← Back to Dashboard</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

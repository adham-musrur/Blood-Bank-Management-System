<?php
session_start();
if (!isset($_SESSION['StaffID'])) {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

// Search logic
$search_id = isset($_GET['staff_id']) ? strtoupper(trim($_GET['staff_id'])) : '';

if (!empty($search_id) && preg_match('/^S\d+$/', $search_id)) {
    $staff_numeric_id = intval(substr($search_id, 1)); // extract numeric part
    $stmt = $conn->prepare("SELECT * FROM staff WHERE StaffID = ?");
    $stmt->bind_param("i", $staff_numeric_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM staff";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff List (Horizontal)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(rgba(255,255,255,0.85), rgba(255,255,255,0.85)), 
                        url('staffbackground.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
        }

        .container {
            margin-top: 50px;
        }

        .contained {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .contained img {
            height: 90px;
            margin-right: 20px;
        }

        .contained h2 {
            font-weight: bold;
            color: #8B0000;
            margin: 0;
        }

        /* Search input animation */
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

        /* HORIZONTAL CARD LAYOUT */
        .staff-scroll {
            display: flex;
            gap: 1rem;
            overflow-x: auto;
            padding-bottom: 1rem;
            scroll-behavior: smooth;
        }
        .staff-scroll::-webkit-scrollbar {
            height: 8px;
        }
        .staff-scroll::-webkit-scrollbar-thumb {
            background: #8B0000;
            border-radius: 4px;
        }
        .staff-card {
            min-width: 260px; /* ensure card has width for horizontal layout */
            border-radius: 12px;
            box-shadow: 0 0 8px rgba(0,0,0,0.15);
            transition: transform 0.2s ease;
        }
        .staff-card:hover {
            transform: translateY(-4px);
        }
        .staff-card .card-header {
            background-color: #8B0000;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }
        .staff-card .card-body p {
            margin-bottom: 0.4rem;
            font-size: 0.9rem;
        }

        /* Print styles */
        @media print {
            .no-print { display: none !important; }
            .staff-scroll { flex-wrap: wrap; overflow: visible; }
            .staff-card { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="contained">
        <div class="d-flex align-items-center">
            <img src="Project logo.png" alt="Logo">
            <h2 class="mb-4 text-center">Staff Members</h2>
        </div>
        <form method="get" class="search-form no-print">
            <input type="text" name="staff_id" class="form-control search-input" placeholder="Search Staff ID (e.g., S01)">
        </form>
    </div>

    <!-- Horizontal scrolling cards -->
    <div class="staff-scroll">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card staff-card">
                    <div class="card-header">
                        <?= 'S' . str_pad($row['StaffID'], 3, '0', STR_PAD_LEFT) ?>
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title mb-2"><?= htmlspecialchars($row['Name']) ?></h5>
                        <p><strong>Username:</strong> <?= htmlspecialchars($row['Username']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($row['Email']) ?></p>
                        <p><strong>Contact:</strong> <?= htmlspecialchars($row['ContactNo']) ?></p>
                        <p><strong>Designation:</strong> <?= htmlspecialchars($row['Designation']) ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-warning w-100">No staff records found.</div>
        <?php endif; ?>
    </div>

    <div class="d-flex justify-content-between mt-4 no-print">
        <a href="dashboard.php" class="btn btn-secondary">← Back</a>
        <button onclick="window.print()" class="btn btn-outline-primary">🖨️ Print</button>
    </div>
</div>

</body>
</html>

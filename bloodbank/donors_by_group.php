<?php
session_start();
if (!isset($_SESSION['StaffID'])) {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

if (!isset($_GET['group_id'])) {
    die("Blood group not specified.");
}

$group_id = intval($_GET['group_id']);

// Get blood group name
$stmt = $conn->prepare("SELECT BloodGroup FROM bloodgroup WHERE BloodGroupID = ?");
$stmt->bind_param("i", $group_id);
$stmt->execute();
$group_result = $stmt->get_result();

if ($group_result->num_rows === 0) {
    die("Blood group not found.");
}

$group = $group_result->fetch_assoc();
$blood_group_name = $group['BloodGroup'];

// Get donors
$stmt = $conn->prepare("SELECT * FROM donor WHERE BloodGroupID = ?");
$stmt->bind_param("i", $group_id);
$stmt->execute();
$donors = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Donors for <?= htmlspecialchars($blood_group_name) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h3 class="mb-3">Blood Group: <?= htmlspecialchars($blood_group_name) ?></h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Donor ID</th>
                <th>Name</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Phone No</th>
                <th>Address</th>
                <th>Last Donation</th>
            </tr>
        </thead>
        <tbody>
            <?php while($donor = $donors->fetch_assoc()): ?>
            <tr>
                <td><?= 'D' . str_pad($donor['DonorID'], 2, '0', STR_PAD_LEFT) ?></td>
                <td><?= htmlspecialchars($donor['Name']) ?></td>
                <td><?= htmlspecialchars($donor['Age']) ?></td>
                <td><?= htmlspecialchars($donor['Gender']) ?></td>
                <td><?= htmlspecialchars($donor['Phone_No']) ?></td>
                <td><?= htmlspecialchars($donor['Address']) ?></td>
                <td><?= htmlspecialchars($donor['LastDonationDate']) ?></td>
            </tr>
            <?php endwhile; ?>
            <?php if ($donors->num_rows === 0): ?>
                <tr><td colspan="7" class="text-center">No donors found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="blood_group.php" class="btn btn-secondary mb-3">← Back</a>
    <button onclick="window.print()" class="btn btn-outline-primary mb-3">🖨️ Print</button>
</div>
</body>
</html>

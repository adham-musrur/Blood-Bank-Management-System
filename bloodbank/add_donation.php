<?php
include 'db_connection.php';
session_start();

$donor_id = $_GET['donor_id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_id = $_SESSION['StaffID'] ?? $_POST['staff_id']; // Prefer session if available
    $date = $_POST['donation_date'];
    $volume = $_POST['volume'];
    $health_status = $_POST['health_status'];

    // 1. Insert into blooddonation table
    $insert_sql = "INSERT INTO blooddonation (DonorID, StaffID, DonationDate, Volume, HealthStatus)
                   VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("iisis", $donor_id, $staff_id, $date, $volume, $health_status);
    
    if ($stmt->execute()) {
        $donation_id = $stmt->insert_id;

        // 2. Update LastDonationDate in donor table
        $conn->query("UPDATE donor SET LastDonationDate = '$date' WHERE DonorID = $donor_id");

        // 3. Get BloodGroupID of this donor
        $bg_sql = "SELECT BloodGroupID FROM donor WHERE DonorID = ?";
        $bg_stmt = $conn->prepare($bg_sql);
        $bg_stmt->bind_param("i", $donor_id);
        $bg_stmt->execute();
        $bg_result = $bg_stmt->get_result();
        $blood_group_id = 0;
        if ($bg_row = $bg_result->fetch_assoc()) {
            $blood_group_id = $bg_row['BloodGroupID'];
        }

        // 4. Insert into bloodstock table
        $stock_sql = "INSERT INTO bloodstock (BloodGroupID, DonationID, Quantity, CollectionDate, ExpiryDate, Cost)
                      VALUES (?, ?, ?, ?, DATE_ADD(?, INTERVAL 42 DAY), ?)";
        $stock_stmt = $conn->prepare($stock_sql);
        $collection_date = $date;
        $expiry_date = $date;
        $cost = 0;

        $stock_stmt->bind_param("iiissd", $blood_group_id, $donation_id, $volume, $collection_date, $expiry_date, $cost);
        $stock_stmt->execute();

        echo "<script>alert('Donation and stock updated successfully.'); window.location.href='donor_list.php';</script>";
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!-- Form to Add Donation -->
<form method="POST">
    <input type="hidden" name="staff_id" value="1"> <!-- Use session in real app -->
    <label>Donation Date:</label>
    <input type="date" name="donation_date" required><br>
    <label>Volume (ml):</label>
    <input type="number" name="volume" required><br>
    <label>Health Status:</label>
    <input type="text" name="health_status" required><br>
    <input type="submit" value="Add Donation">
</form>

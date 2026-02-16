<?php
session_start();
if (!isset($_SESSION['StaffID'])) {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

if (!isset($_GET['id'])) {
    die("Donor ID not specified.");
}

$donor_id = intval($_GET['id']);

// First, delete related donations
if (!$conn->query("DELETE FROM blooddonation WHERE DonorID = $donor_id")) {
    die("Failed to delete related donations: " . $conn->error);
}

// Then delete the donor
$sql = "DELETE FROM donor WHERE DonorID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $donor_id);

if ($stmt->execute()) {
    echo "<script>
        alert('Donor deleted successfully.');
        window.location.href = 'donor_list.php';
    </script>";
} else {
    echo "Delete failed: " . $stmt->error;
}
?>


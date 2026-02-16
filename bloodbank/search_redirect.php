<?php
session_start();
if (!isset($_SESSION['StaffID'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['search_id']) || empty(trim($_GET['search_id']))) {
    header("Location: dashboard.php");
    exit();
}

$search_id = strtoupper(trim($_GET['search_id']));

// Example prefixes and their pages
if (strpos($search_id, 'D') === 0) {
    // Donor ID (like D01)
    header("Location: donor_list.php?donor_id=" . urlencode($search_id));
    exit();
} elseif (strpos($search_id, 'BG') === 0) {
    // BloodGroup ID (like BG03)
    header("Location: blood_group.php?bg_id=" . urlencode($search_id));
    exit();
} elseif (strpos($search_id, 'S') === 0) {
    // Staff ID (like S001)
    header("Location: staff_list.php?staff_id=" . urlencode($search_id));
    exit();
} elseif (strpos($search_id, 'DA') === 0) {
    // Donation ID (like DA01)
    header("Location: blood_donation.php?donation_id=" . urlencode($search_id));
    exit();
} elseif (strpos($search_id, 'ST') === 0) {
    // Stock ID (like ST01)
    header("Location: blood_stock.php?stock_id=" . urlencode($search_id));
    exit();
} else {
    // Unknown ID prefix, redirect back with an error or to dashboard
    header("Location: dashboard.php?error=invalid_id");
    exit();
}
?>

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

// Check ID type — Specific prefixes must come first!
if (strpos($search_id, 'DA') === 0) {
    header("Location: blood_donation.php?donation_id=" . urlencode($search_id));
    exit();
} elseif (strpos($search_id, 'ST') === 0) {
    header("Location: blood_stock.php?stock_id=" . urlencode($search_id));
    exit();
} elseif (strpos($search_id, 'D') === 0) {
    header("Location: donor_list.php?donor_id=" . urlencode($search_id));
    exit();
} elseif (strpos($search_id, 'S') === 0) {
    header("Location: staff_list.php?staff_id=" . urlencode($search_id));
    exit();
} else {
    header("Location: dashboard.php?error=invalid_id");
    exit();
}

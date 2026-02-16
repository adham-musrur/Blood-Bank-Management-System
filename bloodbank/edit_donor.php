<?php
session_start();
if (!isset($_SESSION['StaffID'])) {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

if (!isset($_GET['id'])) {
    die("No Donor ID provided.");
}

$donor_id = intval($_GET['id']);

// Fetch donor data
$stmt = $conn->prepare("SELECT * FROM donor WHERE DonorID = ?");
$stmt->bind_param("i", $donor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows != 1) {
    die("Donor not found.");
}
$donor = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Donor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3 class="mb-4 text-center">Edit Donor</h3>
    <form method="POST" action="update_donor.php">
        <input type="hidden" name="donor_id" value="<?php echo $donor['DonorID']; ?>">

        <div class="mb-3">
            <label>Full Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo $donor['Name']; ?>" required>
        </div>

        <div class="mb-3">
            <label>Age</label>
            <input type="number" name="age" class="form-control" value="<?php echo $donor['Age']; ?>" required>
        </div>

        <div class="mb-3">
            <label>Gender</label>
            <select name="gender" class="form-control" required>
                <option <?php if ($donor['Gender'] == 'Male') echo 'selected'; ?>>Male</option>
                <option <?php if ($donor['Gender'] == 'Female') echo 'selected'; ?>>Female</option>
                <option <?php if ($donor['Gender'] == 'Other') echo 'selected'; ?>>Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Blood Type</label>
            <input type="text" name="bloodtype" class="form-control" value="<?php echo $donor['BloodType']; ?>" required>
        </div>

        <div class="mb-3">
            <label>Phone Number</label>
            <input type="text" name="phone_no" class="form-control" value="<?php echo $donor['Phone_No']; ?>" required>
        </div>

        <div class="mb-3">
            <label>Address</label>
            <textarea name="address" class="form-control" rows="3"><?php echo $donor['Address']; ?></textarea>
        </div>

        <div class="mb-3">
            <label>Last Donation Date</label>
            <input type="date" name="last_donation_date" class="form-control" value="<?php echo $donor['LastDonationDate']; ?>">
        </div>

        <button type="submit" class="btn btn-success w-100">Update Donor</button>
    </form>
</div>
</body>
</html>

<?php
session_start();
if (!isset($_SESSION['StaffID'])) {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

$donor_id = $_POST['donor_id'];
$name = $_POST['name'];
$age = $_POST['age'];
$gender = $_POST['gender'];
$blood_type = $_POST['bloodtype'];
$phone_no = $_POST['phone_no'];
$address = $_POST['address'];
$last_donation_date = $_POST['last_donation_date'];

$sql = "UPDATE donor SET Name=?, Age=?, Gender=?, BloodType=?, Phone_No=?, Address=?, LastDonationDate=?
        WHERE DonorID=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sisssssi", $name, $age, $gender, $blood_type, $phone_no, $address, $last_donation_date, $donor_id);

if ($stmt->execute()) {
    echo "<script>
        alert('Donor updated successfully.');
        window.location.href = 'donor_list.php';
    </script>";
} else {
    echo "Update failed: " . $stmt->error;
}

$conn->close();
?>

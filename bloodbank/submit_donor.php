<?php
session_start();
if (!isset($_SESSION['StaffID'])) {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

// 1. Fetch form data
$name = $_POST['name'];
$age = (int) $_POST['age'];
$gender = $_POST['gender'];
$blood_type = $_POST['blood_type'];
$phone_no = $_POST['phone_no'];
$address = $_POST['address'];
$last_donation_date = $_POST['last_donation_date'];
$staff_id = $_SESSION['StaffID'];

// 2. Get BloodGroupID from bloodgroup table
$sql = "SELECT BloodGroupID FROM bloodgroup WHERE BloodGroup = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $blood_type);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    $blood_group_id = $row['BloodGroupID'];
} else {
    die("Invalid blood group selected.");
}

// 3. Check if donor already exists
$check_sql = "SELECT DonorID FROM donor 
              WHERE Name = ? AND Age = ? AND Gender = ? AND Blood_Type = ? AND Phone_No = ? AND Address = ? AND BloodGroupID = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("sissssi", $name, $age, $gender, $blood_type, $phone_no, $address, $blood_group_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    echo "<script>
        alert('This donor is already registered.');
        window.location.href = 'donor_list.php';
    </script>";
    exit();
}

// 4. Insert into donor table
$insert_sql = "INSERT INTO donor (Name, Age, Gender, Blood_Type, BloodGroupID, Phone_No, Address, LastDonationDate, StaffID)
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$insert_stmt = $conn->prepare($insert_sql);
$insert_stmt->bind_param("sississsi", 
    $name, $age, $gender, $blood_type, $blood_group_id, $phone_no, $address, $last_donation_date, $staff_id
);

if ($insert_stmt->execute()) {
    $donor_id = $insert_stmt->insert_id;

    // 5. Insert into blooddonation table
    $donation_sql = "INSERT INTO blooddonation (DonorID, StaffID, DonationDate, Volume, HealthStatus)
                     VALUES (?, ?, CURDATE(), ?, ?)";
    $donation_stmt = $conn->prepare($donation_sql);
    $volume = 450;
    $healthStatus = 'Good';
    $donation_stmt->bind_param("iiis", $donor_id, $staff_id, $volume, $healthStatus);

    if ($donation_stmt->execute()) {
        $donation_id = $donation_stmt->insert_id;

        // 6. Insert into bloodstock table — one row per donation
        $stock_sql = "INSERT INTO bloodstock (BloodGroupID, DonationID, Quantity, CollectionDate, ExpiryDate, Cost)
                      VALUES (?, ?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 42 DAY), ?)";
        $stock_stmt = $conn->prepare($stock_sql);

        if ($stock_stmt) {
            $cost = 0;
            $stock_stmt->bind_param("iiid", $blood_group_id, $donation_id, $volume, $cost);
            $stock_stmt->execute();

            echo "<script>
                alert('Donor registered and stock added successfully.');
                window.location.href = 'donor_list.php';
            </script>";
        } else {
            echo "Error preparing stock query: " . $conn->error;
        }
    } else {
        echo "Error inserting into blooddonation: " . $donation_stmt->error;
    }
} else {
    echo "Error inserting donor: " . $insert_stmt->error;
}

$conn->close();
?>

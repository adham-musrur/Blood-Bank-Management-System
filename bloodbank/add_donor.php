<?php
session_start();
if (!isset($_SESSION['StaffID'])) {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Donor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f8;
            padding-top: 40px;
        }
        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 6 6 10px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: auto;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <div class="container mb-4 fw-bold text-center">
            <img src="Project logo.png" alt="Blood Bank Logo" style="height: 100px; margin-right: 15px;">
        </div>
        <h4 class="mb-4 text-center">Donor Registration Form</h4>
        <form method="POST" action="submit_donor.php">
            <div class="mb-3">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Age</label>
                <input type="number" name="age" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Gender</label>
                <select name="gender" class="form-control" required>
                    <option value="">Select</option>
                    <option>Male</option>
                    <option>Female</option>
                    <option>Other</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Blood_Type</label>
                <select name="blood_type" class="form-control" required>
                    <option value="">Select</option>
                    <option>O+</option>
                    <option>O-</option>
                    <option>A+</option>
                    <option>A-</option>
                    <option>B+</option>
                    <option>B-</option>
                    <option>AB+</option>
                    <option>AB-</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Phone_No</label>
                <input type="phone" name="phone_no" class="form-control" required>
            </div>


            <div class="mb-3">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="3"></textarea>
            </div>

            <div class="mb-3">
                <label>Last Donation Date</label>
                <input type="date" name="last_donation_date" class="form-control">
            </div>


            <button type="submit" class="btn btn-primary w-100">Submit</button>
        </form>
        <a href="donor_list.php" class="btn btn-secondary mb-3">← Back</a>
    </div>
</div>

</body>
</html>

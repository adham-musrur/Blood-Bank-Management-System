<?php
session_start();
if (!isset($_SESSION['StaffID'])) {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $search_trimmed = trim($search);
    $numeric_id = intval(ltrim($search_trimmed, 'D'));
    $like_search = "%" . $search_trimmed . "%";

    $stmt = $conn->prepare("
        SELECT d.*, b.BloodGroup, COUNT(bd.DonationID) AS DonationCount
        FROM donor d
        JOIN bloodgroup b ON d.BloodType = b.BloodGroup
        LEFT JOIN blooddonation bd ON d.DonorID = bd.DonorID
        WHERE d.DonorID = ? OR d.PhoneNo LIKE ? OR d.Name LIKE ?
        GROUP BY d.DonorID
    ");

    $stmt->bind_param("iss", $numeric_id, $like_search, $like_search);
    $stmt->execute();
    $result = $stmt->get_result();

} else {

    $result = $conn->query("
    SELECT 
        d.DonorID,
        d.StaffID,
        d.Name,
        d.Age,
        d.Gender,
        d.BloodType,
        d.PhoneNo,
        d.Address,
        d.LastDonationDate,
        COUNT(bd.DonationID) AS DonationCount
    FROM donor d
    LEFT JOIN blooddonation bd ON d.DonorID = bd.DonorID
    GROUP BY d.DonorID
");


}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Donor List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            
            background-image: url('loginbackground.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background-image: url('header2.jpg');
            background-size: cover;
            background-position: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .navbar-nav .nav-link {
            font-family: "Montserrat", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-size: 0.95rem;
            letter-spacing: 0.0625em;
            background-color: #FFFFFF;
            border-radius: 10px;
            color: rgb(150, 0, 0 ); !important;
            margin-left: 10px;
        }

        .navbar-nav .nav-link:hover {
            background-color: #820800;
            color: white; !important;
            border-radius: 20px;
        }

        .logo-title {
            display: flex;
            align-items: center;
        }

        .logo-title img {
            height: 60px;
            margin-right: 10px;
        }


        .page-header {

            background-color: #af0000;
            margin-top: 90px;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 0 12px rgba(0,0,0,0.08);
            text-align: center;
        }

        .page-header h2 {
            font-family: "Montserrat", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-weight: 700;
            line-height: 1.2;
            font-size: 2rem;
            color: white;
        }
        
        /* Search */
        .search-container {
            position: relative;
        }

        .search-icon {
            color: white;
            cursor: pointer;
        }

        /* Hide input initially */
        .search-input {
            width: 0;
            padding: 0;
            border: none;
            background: transparent;
            color: white;
            transition: width 0.4s ease, padding 0.3s ease;
            box-shadow: none;
            outline: none;
        }

        /* Expand when hovering on container (icon or input area) */
        .search-container:hover .search-input,
        .search-input:focus {
            width: 220px;
            padding: 0.375rem 0.75rem;
            border: 1px solid #ccc;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
            color: #000;
        }

        .row2 {
            margin-top: 40px;
        }

        .card {
    border: none;
    border-radius: 15px;
    height: 100%;
    transition: transform 0.2s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.card-body {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* Button spacing */
.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}

/* Responsive scaling */
@media (max-width: 992px) {
    .card h6, .card p, .card div {
        font-size: 0.85rem;
    }
}

    .btn-group-sm > .btn {
        padding: 0.2rem 0.4rem;
    }
}


        .btn-sm {
            border-radius: 10px;
        }

        .badge.bg-danger {
            background-color: #dc3545 !important;
            font-size: 0.9em;
            padding: 5px 10px;
        }

        .print-btn, .no-print {
            background-color: white;
            color: #af0000;
            margin-top: 20px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }

    </style>
</head>



 <!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid px-4">
        <div class="logo-title">
            <img src="Project logo.png" alt="Blood Bank Logo">
            <span class="fs-4 fw-bold">Blood Bank Management System</span>
        </div>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
            <span class="navbar-toggler-icon"></span>
        </button>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
            <ul class="navbar-nav align-items-center">
                      <li class="nav-item"><a class="nav-link" href="dashboard.php"><b>Dashboard</b></a></li>
                      <li class="nav-item"><a class="nav-link" href="blood_donation.php"><b>Blood Donation</b></a></li>
                      <li class="nav-item"><a class="nav-link" href="blood_group.php"><b>Blood Group</b></a></li>
                      <li class="nav-item"><a class="nav-link" href="blood_stock.php"><b>Blood Stock</b></a></li>
                      <li class="nav-item"><a class="nav-link" href="stock_out.php"><b>Stock Out</b></a></li>
                      <li class="nav-item"><a class="nav-link" href="staff_list.php"><b>Staff</b> </a></li>
                      <li class="nav-item"><a class="nav-link text-danger" href="logout.php"><b>Logout</b></a></li>
                      <li class="nav-item ms-3">
                        <div class="search-container d-flex align-items-center">
                            <form method="get" class="search-form no-print" action="donor_list.php">
                                <input type="text" name="search" class="form-control search-input" placeholder="Search by Donor ID, Name, or Phone No;" />
                            </form>
                            <i class="bi bi-search me-2 search-icon" style="font-size: 1.2rem;"></i>
                        </div>
                    </li>

            </ul>
        </div>
    </div>
</nav>
<div class="container page-header">
    <h2>Donor List</h2>
</div>

 <div class="row2">
<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <!-- Donor Cards Container -->
<div class="container mt-4">
    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4 col-xl-4 mb-4">
                    <div class="card h-100 p-2 shadow-sm">
                        <div class="card-body py-3 px-3">
                            <!-- Donor ID -->
                            <!-- Donor ID Box - Deep Red with White Text -->
                            <div class="text-center mb-2 rounded py-2 px-2" style="background-color: #8B0000;">
                                <strong class="text-white">Donor ID: D<?= str_pad($row['DonorID'], 2, '0', STR_PAD_LEFT) ?></strong>
                            </div>

                            <!-- Name -->
                            <div class="p-2 mb-2 bg-light border rounded">
                                <strong>Name:</strong> <?= htmlspecialchars($row['Name']) ?>
                            </div>

                            <!-- Age and Gender -->
                            <div class="p-2 mb-2 bg-light border rounded d-flex justify-content-between">
    <div><strong>Age:</strong> <?= $row['Age'] ?></div>
    <div><strong>Gender:</strong> <?= $row['Gender'] ?></div>
</div>

<!-- Blood Group -->
<div class="p-2 mb-2 bg-light border rounded d-flex justify-content-between">
    <div>
        <strong>Blood Group:</strong>
        <span class="badge bg-danger"><?= $row['BloodType'] ?></span>
    </div>
</div>

<!-- Phone -->
<div class="p-2 mb-2 bg-light border rounded">
    <strong>Phone No:</strong> <?= $row['PhoneNo'] ?>
</div>

<!-- Address -->
<div class="p-2 mb-2 bg-light border rounded">
    <strong>Address:</strong> <?= $row['Address'] ?>
</div>

<!-- Last Donation -->
<div class="p-2 mb-2 bg-light border rounded">
    <strong>Last Donation:</strong> <?= $row['LastDonationDate'] ?>
</div>

<!-- Staff -->
<div class="p-2 mb-2 bg-light border rounded">
    <strong>Staff ID:</strong>
    <a href="staff_list.php?id=<?= urlencode($row['StaffID']) ?>" class="text-decoration-none text-dark">
        S<?= str_pad($row['StaffID'], 2, '0', STR_PAD_LEFT) ?>
    </a>
</div>


                            <!-- Buttons -->
                            <div class="d-flex justify-content-between align-items-center mt-2 no-print">
                                <span class="fw-bold small">Donations: <?= $row['DonationCount']; ?></span>
                                <div class="btn-group btn-group-sm" role="group" style="gap: 4px;">
                                    <a href="add_donation.php?donor_id=<?= $row['DonorID'] ?>" class="btn btn-success" title="Add Donation">➕</a>
                                    <a href="edit_donor.php?id=<?= $row['DonorID'] ?>" class="btn btn-warning">✏️</a>
                                    <a href="delete_donor.php?id=<?= $row['DonorID'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this donor?');">🗑️</a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center text-danger">No donor found for the given ID.</div>
        <?php endif; ?>
    </div>
</div>

    <?php endwhile; ?>
<?php else: ?>
    <div class="col-12 text-center text-danger">No donor found for the given ID.</div>
<?php endif; ?>
</div>
    <div class="d-flex justify-content-between mt-4">
    <a href="add_donor.php" class="btn btn-success no-print">+ Add Donor</a>
    <button onclick="window.print()" class="btn btn-outline-primary no-print">🖨️ Print</button>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</div>
</body>
</html>

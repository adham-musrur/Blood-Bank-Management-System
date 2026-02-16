<?php
session_start();
if (!isset($_SESSION['StaffID'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Blood Bank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('background.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background-image: url('navbar-bg.jpg');
            background-size: cover;
            background-position: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .navbar-nav .nav-link {
            background-color: #831700;
            border-radius: 10px;
            color: white !important;
            margin-left: 10px;
        }

        .navbar-nav .nav-link:hover {
            background-color: #fccec4;
            color: black !important;
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

        .main-content {
            padding: 100px 20px 30px;
            flex: 1 0 auto;
        }

        .section-card {
            padding: 20px;
            background: rgba(165, 255, 175, 0);
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        #searchForm {
            position: relative;
        }

        #searchBox {
            width: 0;
            transition: width 0.5s;
        }

        #searchForm:hover #searchBox, 
        #searchBox:focus {
            width: 250px;
        }

        footer {
    width: 1365px;
    height: 180px;
    margin: 0 auto;
    background-color: #ffffff;
    color: #ddd;
    padding: 30px 30px;
    box-sizing: border-box;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

footer {
    width: 100%;
    background-color: #ffffff;
    color: #000000;
    padding: 30px;
    box-sizing: border-box;
    font-size: 0.95rem;
    margin-top: auto;
}

.footer-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 30px;
}

.footer-column {
    flex: 1;
    min-width: 280px;
}

.footer-column h4 {
    color: #8B0000;
    margin-bottom: 15px;
}

.footer-column ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-column ul li {
    margin-bottom: 8px;
}

footer a {
    color: #000000;
    text-decoration: none;
}

footer a:hover {
    text-decoration: underline;
}

}
    </style>
</head>
<body>


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

        <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item"><a class="nav-link" href="donor_list.php">Donor</a></li>
                <li class="nav-item"><a class="nav-link" href="blood_donation.php">Blood Donation</a></li>
                <li class="nav-item"><a class="nav-link" href="blood_group.php">Blood Group</a></li>
                <li class="nav-item"><a class="nav-link" href="blood_stock.php">Blood Stock</a></li>
                <li class="nav-item"><a class="nav-link" href="staff_list.php">Staff </a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
                <li class="nav-item ms-3">
                    <form id="searchForm" action="search_router.php" method="get">
                        <input type="text" name="search_id" class="form-control" id="searchBox" placeholder="Search ID...." />
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container main-content">
    <div class="section-card text-center">
        <h1 class="fw-bold mb-3">Welcome, <?= htmlspecialchars($_SESSION['Username']) ?>!</h1>
        <p>Select a section from the top menu to manage donors, blood donation, blood groups, or check blood stock.</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

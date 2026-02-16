<?php
session_start();
if (!isset($_SESSION['StaffID'])) {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

$sql = "SELECT BloodGroupID, BloodGroup FROM bloodgroup";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Blood Group Overview</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f9f9fb;
      font-family: 'Segoe UI', sans-serif;
    }

      .top-bar {
    background: #8b0000; /* dark red */
    color: white;
    padding: 12px 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 8px rgba(139, 0, 0, 0.4);
  }

  /* Logo and title */
  .logo-title {
    display: flex;
    align-items: center;
    gap: 15px;
  }

  .top-bar img {
    height: 50px;
  }

  .title {
    font-size: 1.6rem;
    font-weight: 700;
    letter-spacing: 1px;
    user-select: none;
  }

  /* Navigation links */
  .nav-links {
    display: flex;
    align-items: center;
    gap: 25px;
    font-weight: 600;
  }

  .nav-links a {
    color: white;
    text-decoration: none;
    position: relative;
    padding-bottom: 4px;
    transition: color 0.3s;
  }

  .nav-links a:hover,
  .nav-links a.active {
    color: #ffd6d6;
  }

  /* Underline effect on hover */
  .nav-links a::after {
    content: "";
    position: absolute;
    width: 0%;
    height: 2px;
    background: #ffd6d6;
    left: 0;
    bottom: 0;
    transition: width 0.3s;
  }

  .nav-links a:hover::after,
  .nav-links a.active::after {
    width: 100%;
  }

  /* Responsive hamburger */
  @media (max-width: 767px) {
    .nav-links {
      display: none; /* Hide nav links on small */
    }
  }

    .group-card {
      background-color: #fff;
      border: 1px solid #eee;
      padding: 25px;
      border-radius: 14px;
      transition: transform 0.3s, box-shadow 0.3s;
      text-align: center;
    }

    .group-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.08);
    }

    .blood-symbol {
      font-size: 2.8rem;
      font-weight: bold;
      color: #b30000;
    }

    .group-id {
      font-size: 0.9rem;
      font-weight: 500;
      color: #777;
      margin-bottom: 6px;
    }

    .view-link {
      font-size: 0.85rem;
      color: #555;
      text-decoration: none;
    }

    .view-link:hover {
      color: #000;
    }

    .btn-back {
      background-color: #831700;
      color: #fff;
      border-radius: 8px;
      padding: 10px 24px;
    }

    .btn-back:hover {
      background-color: #5a0000;
    }

  </style>
</head>
<body>

<!-- Top Navigation Bar -->
<div class="top-bar">
  <div class="logo-title">
    <img src="Project logo.png" alt="Blood Bank Logo" />
    <div class="title">Blood Group Overview</div>
  </div>
  <nav class="nav-links">
    <a href="dashboard.php">Dashboard</a>
    <a href="donor_list.php">Donor</a>
    <a href="blood_donation.php">Blood Donation</a>
    <a href="blood_stock.php">Blood Stock</a>
    <a href="staff_list.php">Staff</a>
    <a href="logout.php" class="text-warning">Logout</a>
  </nav>
</div>


<!-- Blood Groups Display -->
<div class="container mt-5">
  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
    <?php while($row = $result->fetch_assoc()): ?>
      <div class="col">
        <a href="donors_by_group.php?group_id=<?= urlencode($row['BloodGroupID']) ?>" style="text-decoration: none;">
          <div class="group-card">
            <div class="blood-symbol"><?= htmlspecialchars($row['BloodGroup']) ?></div>
            <div class="group-id">ID: <?= 'BG' . str_pad($row['BloodGroupID'], 2, '0', STR_PAD_LEFT) ?></div>
            <div><span class="view-link">View all donors</span></div>
          </div>
        </a>
      </div>
    <?php endwhile; ?>
  </div>

  <div class="text-center mt-5">
    <a href="dashboard.php" class="btn btn-back">← Back to Dashboard</a>
  </div>
</div>

</body>
</html>

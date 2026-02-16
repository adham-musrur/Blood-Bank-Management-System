<?php
session_start();
include('db_connection.php');

$errMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action']; // login or signup
    $username = trim($_POST['Username']);
    $password = trim($_POST['Password']);

    if ($action === 'signup') {
        $name = trim($_POST['Name']);
        $designation = trim($_POST['Designation']);
        $email = trim($_POST['Email']);
        $contact_no = trim($_POST['Contact_No']);

        // Check if username exists
        $stmt = $conn->prepare("SELECT * FROM staff WHERE Username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errMsg = "Username already exists!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            $insert = $conn->prepare("INSERT INTO staff (Name, Username, Password, Designation, Email, Contact_No) VALUES (?, ?, ?, ?, ?, ?)");
            if (!$insert) {
                die("SQL error: " . $conn->error);
            }

            $insert->bind_param("ssssss", $name, $username, $hashed_password, $designation, $email, $contact_no);
            $insert->execute();

            $_SESSION['StaffID'] = $conn->insert_id;
            $_SESSION['Username'] = $username;

            header("Location: dashboard.php");
            exit();
        }

    } elseif ($action === 'login') {
        $stmt = $conn->prepare("SELECT * FROM staff WHERE Username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['Password'])) {
            $_SESSION['StaffID'] = $user['StaffID'];
            $_SESSION['Username'] = $user['Username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $errMsg = "Invalid username or password!";
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Staff Login - Blood Bank</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-box {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        .form-toggle {
            text-align: center;
            margin-top: 10px;
            font-size: 0.9rem;
        }

        .form-toggle a {
            cursor: pointer;
            color: #007bff;
        }

        .signup-fields {
            display: none;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h4 class="text-center mb-4" id="form-title">Staff Login</h4>
    <?php if ($errMsg): ?>
        <div class="alert alert-danger"><?= $errMsg ?></div>
    <?php endif; ?>

    <form method="POST" id="loginForm">
        <input type="hidden" name="action" id="formAction" value="login">

        <div class="mb-3 signup-fields">
            <label>Full Name</label>
            <input type="text" name="Name" class="form-control">
        </div>

        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="Username" required class="form-control">
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="Password" required class="form-control">
        </div>

        <div class="mb-3 signup-fields">
            <label>Designation</label>
            <input type="text" name="Designation" class="form-control">
        </div>

        <div class="mb-3 signup-fields">
            <label>Email</label>
            <input type="email" name="Email" class="form-control">
        </div>

        <div class="mb-3 signup-fields">
            <label>Contact_No</label>
            <input type="text" name="Contact_No" class="form-control">
        </div>

        <button type="Submit" class="btn btn-primary w-100" id="submitBtn">Login</button>
    </form>

    <div class="form-toggle mt-3">
        <span id="toggleText">New staff? <a onclick="toggleForm()">Sign up</a></span>
    </div>
</div>

<script>
function toggleForm() {
    const isLogin = document.getElementById("formAction").value === "login";
    document.getElementById("form-title").innerText = isLogin ? "Staff Signup" : "Staff Login";
    document.getElementById("formAction").value = isLogin ? "signup" : "login";
    document.getElementById("submitBtn").innerText = isLogin ? "Sign Up" : "Login";
    document.getElementById("toggleText").innerHTML = isLogin
        ? 'Already registered? <a onclick="toggleForm()">Login</a>'
        : 'New staff? <a onclick="toggleForm()">Sign up</a>';

    document.querySelectorAll('.signup-fields').forEach(el => {
        el.style.display = isLogin ? 'block' : 'none';
        el.querySelector('input')?.toggleAttribute('required', isLogin);
    });
}
</script>

</body>
</html>
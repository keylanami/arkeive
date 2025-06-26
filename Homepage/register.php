<?php 
session_start();
require_once "koneksi.php";

$errors = [];
$fullname = $username = $email = $password = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $verify = trim($_POST['verify-password']);

    // Basic backend validation
    if (empty($fullname)) {
        $errors[] = "Full name is required.";
    }
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email is required.";
    }
    if (empty($password) || !preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $errors[] = "Password must be at least 8 characters long and include letters and numbers.";
    }
    
    if ($password != $verify){
        $errors[] = "Password doesn't match.";
    }

    // Check for existing username/email
    if (empty($errors)) {
        $check = mysqli_prepare($conn, "SELECT userId FROM users WHERE username = ? OR email = ?");
        mysqli_stmt_bind_param($check, "ss", $username, $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $errors[] = "Username or email is already taken.";
        }

        mysqli_stmt_close($check);
    }

    // Proceed with insert if no errors
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $sql_insert = "INSERT INTO users (username, full_name, email, password) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql_insert);
        mysqli_stmt_bind_param($stmt, "ssss", $username, $fullname, $email, $hashedPassword);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['registered'] = "Registered successfully";
            header("Location: login.php");
            exit();
        } else {
            $errors[] = "Database error: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .register-container {
            text-align: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        h1 {
            color: #007bff;
        }
        input[type="text"], input[type="password"], input[type="email"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .login-link {
            margin-top: 10px;
            display: block;
            color: #007bff;
            text-decoration: none;
        }
        .login-link:hover {
            text-decoration: underline;
        }
        .error-message {
            color: red;
            font-size: 0.9em;
            text-align: left;
            margin-bottom: 10px;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordField = document.getElementById('password');
            const toggleButton = document.getElementById('togglePassword');
            const eyeIcon = document.getElementById('eyeIcon');

            toggleButton.addEventListener('mousedown', function () {
                passwordField.type = 'text';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            });

            toggleButton.addEventListener('mouseup', function () {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            });

            toggleButton.addEventListener('mouseleave', function () {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            });
        });
        </script>
</head>
<body>
<div class="register-container">
    <h1>Register</h1>

    <?php if (!empty($errors)): ?>
        <div class="error-message">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li id="error-message"><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <input type="text" name="fullname" placeholder="Full Name" value="<?php echo $fullname?>" required>
        <input type="text" name="username" placeholder="Username" value="<?php echo $username?>" required>
        <input type="email" name="email" placeholder="Email" value="<?php echo $email?>" required>
        <div style="position: relative;">
            <input type="password" name="password" id="password" placeholder="Password" required>
            <button type="button" id="togglePassword" style="position: absolute; right: 10px; top: 40%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
                <i class="fa-solid fa-eye-slash" id="eyeIcon"></i>
            </button>
        </div>  
        <input type="password" name="verify-password" id="verify-password" placeholder="Verify Password" required>
        <input type="submit" value="Register">
    </form>
    <a href="login.php" class="login-link">Back to Login</a>
</div>
</body>
</html>

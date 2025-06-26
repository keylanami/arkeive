<?php
require_once "koneksi.php";
session_start();
$registeredMsg = $_SESSION['registered'] ?? null;
unset($_SESSION['registered']);
if ($_SERVER["REQUEST_METHOD"]=="POST"){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn,$sql);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password,$user['password'])){
        $_SESSION['user_id'] = $user['userId'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit();
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>

<html>
<head>
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
        .login-container {
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
        input[type="text"], input[type="password"] {
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
        .register-link {
            margin-top: 10px;
            display: block;
            color: #007bff;
            text-decoration: none;
        }
        .register-link:hover {
            text-decoration: underline;
        }
        .error-message {
            color: #ff0000;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
    <script>
        //Script pop up message disini
    </script>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <?php if ($registeredMsg): ?>
    <p style="color: green;"><?php echo htmlspecialchars($registeredMsg); ?></p>
<?php endif; ?>

        <form action="login.php" method="POST">
            Username: <input type="text" name="username" required><br>
            Password: <input type="password" name="password" required><br>
            <input type="submit" value="Login">
        </form>
        <a href="register.php">Register if you don't have an account</a>
        <?php
        if (!empty($error_message)) {
            echo '<div class="error-message">' . htmlspecialchars($error_message) . '</div>';
        }
        ?>
    </div>
</body>
</html>
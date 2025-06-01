<?php
    session_start();

    include 'user_functions.php';

    $error_message = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['username'];
        $pass = $_POST['password'];

        if(empty($name) || empty($pass)) {
            $error_message = "Ви не заповнили всі поля";
        }

        $userid = login($name, $pass);
        if(!$userid) {
            $error_message = "Користувача не існує або пароль невірний";
        } else {
            $_SESSION['userid'] = $userid;
            $error_message = "";
            updateLoginTime($userid);
            header ( 'Location: profile.php' );
            exit;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f2f2f2;
        }
        .login-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        .login-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .login-container button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <form action="#" method="post">
            <label for="username">User Name</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <?php if (!empty($error_message)): ?>
            <?php
                echo "<p style=\"color: red\">$error_message</p>";
            ?>
        <?php endif; ?>
    </div>
</body>
</html>
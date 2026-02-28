<?php
    include "../../db/db.php";
    $username = "admin";
    $password = "admin123";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $inputUsername = $_POST["username"] ?? '';
        $inputPassword = $_POST["password"] ?? '';

        if(empty($inputUsername) || empty($inputPassword)) {
            echo "All fields are required.";
            exit();
        }

        if ($inputUsername !== $username || $inputPassword !== $password) {
            echo "Not applicable";
            exit();
        }

        $createTable = "CREATE TABLE IF NOT EXISTS admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL
        )";
        mysqli_query($conn, $createTable);

        $checkSql = "SELECT * FROM admins WHERE username='" . mysqli_real_escape_string($conn, $inputUsername) . "'";
        $result = mysqli_query($conn, $checkSql);
        
        if ($result->num_rows == 0) {
            $hashedPassword = password_hash($inputPassword, PASSWORD_DEFAULT);
            $insertSql = "INSERT INTO admins (username, password) VALUES ('" . mysqli_real_escape_string($conn, $inputUsername) . "', '$hashedPassword')";
            mysqli_query($conn, $insertSql);
        }

        header("Location: ../dashboard/dashboard.php");
        exit();
    }
    ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign in Page</title>
    <link rel="icon" href="../../assets/images/logo3.png" type="image/png">

    <style>
        /* Modern CSS Reset & Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: url('httsthetic-bookps://www.freepik.com/free-photos-vectors/ae');

        }
        .lib {
            background: url('https://epe.brightspotcdn.com/6e/90/f69810544a87a036b320166f23a3/books-471922991.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        /* Container with the rounded border effect seen in image */
        .page-wrapper {
            width: 100%;
            max-width: 1200px;
            height: 90vh;
            backdrop-filter: blur(1px);
            border-radius: 40px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            position: relative;
        }

        /* Left Branding Side */
        .branding-side {
            flex: 1;
            position: relative;
            
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 60px;
            color: #000000;
            overflow: hidden;
            backdrop-filter: blur;;
        }

        /* Abstract Background (Simulated with Gradient since external image isn't available) */
        .branding-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
        }

        .branding-content {
            position: relative;
            z-index: 3;
        }

        .quote-label {
            font-size: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
            opacity: 0.8;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .quote-label::after {
            content: '';
            height: 1px;
            width: 80px;
            background: #fff;
            margin-left: 15px;
            display: inline-block;
        }

        .main-headline {
            font-size: 64px;
            font-weight: 500;
            line-height: 1.1;
            margin-top: auto;
            margin-bottom: 60px;
            font-family: serif; /* Mimicking the elegant serif look */
        }

        /* Right Login Side */
        .login-side {
            flex: 1;
            background: #ece7d1;
            display: flex;
            flex-direction: column;
            padding: 60px;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .logo-section {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 60px;
            font-weight: 600;
            font-size: 20px;
        }

        .logo-icon {
            width: 24px;
            height: 24px;
            background: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .welcome-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .welcome-header h1 {
            font-size: 42px;
            font-family: serif;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .welcome-header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 8px;
            color: #333;
        }

        .input-wrapper {
            position: relative;
        }
        
       
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            background-color: #f8f9fc;
            border: 1px solid #eee;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.2s;
        }

        input:focus {
            outline: none;
            border-color: #000;
            background-color: #fff;
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            opacity: 0.5;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            margin-bottom: 30px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .forgot-password {
            color: #333;
            text-decoration: none;
            font-weight: 500;
        }

        .btn {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.1s, opacity 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: none;
        }

         .btn-secondary {
            background: #fff;
            color: #333;
            border: 1px solid #eee;
        }

        .btn-secondary img {
            width: 18px;
        }

        .btn:active {
            transform: scale(0.98);
        }

        .btn-primary {
            background: #000;
            color: #fff;
            margin-bottom: 12px;
        }

        .footer-text {
            text-align: center;
            margin-top: 60px;
            font-size: 14px;
            color: #666;
        }

        .footer-text a {
            color: #000;
            text-decoration: none;
            font-weight: 700;
        }

        /* Responsive adjustments */
        @media (max-width: 900px) {
            .branding-side {
                display: none;
            }
            .page-wrapper {
                max-width: 500px;
                height: auto;
                min-height: 600px;
            }
        }
    </style>
</head>
<body class="lib">
      <div class="page-wrapper">
        <!-- Left Branding Section -->
        <div class="branding-side">
            <div class="branding-content">
                <div class="quote-label">A Wise Quote</div>
            </div>
            
            <div class="branding-content" style="margin-top: auto;">
                <h2 class="main-headline">Get<br>Everything<br>You Want</h2>
            </div>
        </div>

        <!-- Right Login Section -->
        <div class="login-side">
            <div class="login-container">
                
                <div class="logo-section">
                    <div class="logo-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-leaf" viewBox="0 0 16 16">
  <path d="M1.4 1.7c.216.289.65.84 1.725 1.274 1.093.44 2.884.774 5.834.528l.37-.023c1.823-.06 3.117.598 3.956 1.579C14.16 6.082 14.5 7.41 14.5 8.5c0 .58-.032 1.285-.229 1.997q.198.248.382.54c.756 1.2 1.19 2.563 1.348 3.966a1 1 0 0 1-1.98.198c-.13-.97-.397-1.913-.868-2.77C12.173 13.386 10.565 14 8 14c-1.854 0-3.32-.544-4.45-1.435-1.125-.887-1.89-2.095-2.391-3.383C.16 6.62.16 3.646.509 1.902L.73.806zm-.05 1.39c-.146 1.609-.008 3.809.74 5.728.457 1.17 1.13 2.213 2.079 2.961.942.744 2.185 1.22 3.83 1.221 2.588 0 3.91-.66 4.609-1.445-1.789-2.46-4.121-1.213-6.342-2.68-.74-.488-1.735-1.323-1.844-2.308-.023-.214.237-.274.38-.112 1.4 1.6 3.573 1.757 5.59 2.045 1.227.215 2.21.526 3.033 1.158.058-.39.075-.782.075-1.158 0-.91-.288-1.988-.975-2.792-.626-.732-1.622-1.281-3.167-1.229l-.316.02c-3.05.253-5.01-.08-6.291-.598a5.3 5.3 0 0 1-1.4-.811"/>
</svg>
                        
                    </div>
                    NeonLeaf
                </div>

                <div class="welcome-header">
                    <h1>Admin</h1>
                    
                </div>

                <form method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <div class="input-wrapper">
                            <input type="text" id="username" name="username" required placeholder="Enter your username">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" required placeholder="Enter your password">
                            <span class="toggle-password">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </span>
                        </div>
                    </div>


                    <button type="submit" class="btn btn-primary">Sign in</button>
                                      
                </form>

                

                <div class="footer-text">
                     User sign in? <a href="#">Sign In</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
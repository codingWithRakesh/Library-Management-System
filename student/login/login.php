<?php
    include "../../db/db.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST["email"] ?? '';
        $password = $_POST["password"] ?? '';

        if(empty($email) || empty($password)) {
            $error_message = "All fields are required.";
        } else {
            $sql = "SELECT * FROM students WHERE email='$email'";
            $result = mysqli_query($conn, $sql);
            
            if ($result->num_rows > 0) {
                $row = mysqli_fetch_assoc($result);
            
                if (password_verify($password, $row["password"])) {
                    header("Location: ../../home/index.php");
                    exit();
                } else {
                    $error_message = "Invalid password. Please try again.";
                }
            } else {
                $error_message = "No account found with that email. Please sign up.";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login Page</title>

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
            background: url('https://img.freepik.com/premium-photo/books-decorative-dried-flowers-glass-bottle_380164-275017.jpg?semt=ais_user_personalization&w=740&q=80') no-repeat center center fixed;
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
            background: #fff;
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
            background: #000;
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

        input[type="email"],
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
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="white"><path d="M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71z"/></svg>
                    </div>
                    Cogie
                </div>

                <div class="welcome-header">
                    <h1>Welcome</h1>
                    
                </div>

                <form method="post">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-wrapper">
                            <input type="email" id="email" name="email" required placeholder="Enter your email">
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

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox"> Remember me
                        </label>
                        <a href="#" class="forgot-password">Forgot Password</a>
                    </div>

                    <button type="submit" class="btn btn-primary">Sign In</button>

                    <button type="button" class="btn btn-secondary">
                       <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                        </svg>
                        Admin Login
                    </button>
                    
                   
                </form>

                 <?php if (isset($error_message)): ?>
                    <div style="color: red; text-align: center; margin-bottom: 20px;">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div> 
                <?php endif; ?>

                <div class="footer-text">
                    Don't have an account? <a href="#">Sign Up</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
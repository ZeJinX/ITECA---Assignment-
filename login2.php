<?php
// Start the session and this gives the user their digital ID badge
session_start();

// Connect to the database

require 'includes/db.php';

$message = ""; // This will hold error messages if they type the wrong password

// Check if the form was submitted

if ($_SERVER["REQUEST_METHOD"] == "POST") 
    {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Look for the user in the database by their email
    $stmt = $conn->prepare("SELECT user_id, full_name, password_hash, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if we found exactly one matching email
    if ($result->num_rows === 1) 
        {
        $user = $result->fetch_assoc();

        // Check if the password they typed matches the hashed password in the database
        if (password_verify($password, $user['password_hash'])) 
            {
            
            // SUCCESS! Give them their session ID badge
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            // Redirect them to the correct dashboard!
            if ($user['role'] == 'admin')
                {
                header("Location: admin-overview.php");
            } 
            else 
            {
                header("Location: dashboard.php");
            }
            exit(); // Stop running code after redirect

        } 
        else 
        {
            // Wrong password
            $message = "<div class='error-message'>ERROR: Incorrect password.</div>";
        }
    } 
    else 
    {
        // Email not in database
        $message = "<div class='error-message'>ERROR: No account found with that email.</div>";
    }
    $stmt->close();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - PRINT.IT</title>
    <link rel="stylesheet" href="css/style2.css?v=3">
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <div class="login-wrapper">
        <div class="neo-box login-box">

            <h1 class="login-heading">GET IN</h1>
            <p class="login-subheading">Are you ready to print some magic?</p>
             
            <h1 class="login-heading">Welcome Back</h1>
            <p class="login-subheading">Log in to manage your 3D prints.</p>

            <?php echo $message; ?>

            <form action="login.php" method="POST">

                <div class="form-group">
                    <label class="form-label">EMAIL ADDRESS</label>
                    <input type="email" name="email" class="neo-input" placeholder="print@example.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label">PASSWORD</label>
                    <input type="password" name="password" class="neo-input" placeholder="Your secret password" required>
                </div>

                <button type="submit" class="neo-btn bg-cyan w-100 login-action-btn">LOG IN</button>

            </form>

            <hr class="divider">

            <p class="signup-text">Don't have an account yet?</p>
            <a href="signup.php" class="neo-btn bg-pink w-100 signup-link-btn">SIGN UP HERE</a>
            
            <p class="hint-text">Hint: Log in with your email "admin@print.it" to access the admin dashboard</p>
                
        </div>
     </div>

     <script src="js/main.js"></script>
</body>
</html>
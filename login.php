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

    // Look for the user in the database by their email. 
    $stmt = $conn->prepare("SELECT user_id, full_name, password_hash, role, status FROM users WHERE email = ?");
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
            
            
            // If the database says they are suspended, stop them right here.
            if (isset($user['status']) && strtolower($user['status']) === 'suspended') 
            {
                $message = "<div style='background-color: #FF4980; color: white; border: 3px solid black; padding: 15px; font-weight: 900; margin-bottom: 20px; text-transform: uppercase;'><i class='fa-solid fa-ban' style='margin-right:8px;'></i> ERROR: Your account has been suspended by an admin.</div>";
            }
            else 
            {
                // Give them their session ID badge
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                // Redirect them to the correct dashboard
                if ($user['role'] == 'admin')
                {
                    header("Location: admin-overview.php");
                } 
                else 
                {
                    header("Location: dashboard.php");
                }
                exit();
            }

        } 
        else 
        {
            // Wrong password
            $message = "<div style='background-color: #FF4980; color: white; border: 3px solid black; padding: 15px; font-weight: 900; margin-bottom: 20px; text-transform: uppercase;'><i class='fa-solid fa-triangle-exclamation' style='margin-right:8px;'></i> ERROR: Incorrect password.</div>";
        }
    } 
    else 
    {
        // Email not in database
        $message = "<div style='background-color: #FF4980; color: white; border: 3px solid black; padding: 15px; font-weight: 900; margin-bottom: 20px; text-transform: uppercase;'><i class='fa-solid fa-triangle-exclamation' style='margin-right:8px;'></i> ERROR: No account found with that email.</div>";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=3">
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <div class="login-wrapper">
        <div class="neo-box login-box">

            <h1 style="font-weight: 900; font-size: 36px; margin-bottom: 5px;">GET IN</h1>
            <p style="font-weight: 700; color: #64748B; margin-bottom: 30px;">Are you ready to print some magic?</p>
             
            <h1 style="font-weight: 900; font-size: 36px; margin-bottom: 5px; text-transform: uppercase;">Welcome Back</h1>
            <p style="font-weight: 700; color: #6B7280; margin-bottom: 30px; font-size: 14px;">Log in to manage your 3D prints.</p>

            <?php echo $message; ?>

            <form action="login.php" method="POST">

                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-envelope" style="margin-right:5px;"></i> EMAIL ADDRESS</label>
                    <input type="email" name="email" class="neo-input" placeholder="print@example.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-key" style="margin-right:5px;"></i> PASSWORD</label>
                    <input type="password" name="password" class="neo-input" placeholder="Your secret password" required>
                </div>

                <button type="submit" class="neo-btn bg-cyan w-100" style="font-size: 16px; margin-top: 10px; padding: 15px;">
                    <i class="fa-solid fa-right-to-bracket" style="margin-right:8px;"></i> LOG IN
                </button>

            </form>

            <hr class="divider">

            <p style="font-weight: 700; margin-bottom: 15px; font-size: 14px;">Don't have an account yet?</p>
            <a href="signup.php" class="neo-btn bg-pink w-100" style="font-size: 14px; padding: 15px;">
                <i class="fa-solid fa-user-plus" style="margin-right:8px;"></i> SIGN UP HERE
            </a>
            
                
        </div>
     </div>
     <?php include 'includes/footer.php'; ?>

     <script src="js/main.js"></script>
</body>
</html>
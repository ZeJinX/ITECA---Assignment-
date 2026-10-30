<?php
session_start();
require 'includes/db.php';

// Check if user is actually logged in
if (!isset($_SESSION['user_id']))
{
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Catch the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch the user's current password hash from the database
    $stmt = $conn->prepare("SELECT password_hash FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify the current password they typed matches the one on file
    if (password_verify($current_password, $user['password_hash'])) 
        {
        
        // Ensure the two new passwords match exactly
        if ($new_password === $confirm_password) 
            {
            
            // Hash the brand new password for security
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update the database
            $update_stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
            $update_stmt->bind_param("si", $new_hash, $user_id);
            
            if ($update_stmt->execute()) 
            {
                $message = "<div style='background-color: #B4FF36; border: 3px solid black; padding: 15px; font-weight: 900; margin-bottom: 20px; text-transform: uppercase;'><i class='fa-solid fa-check' style='margin-right:8px;'></i> SUCCESS: Password updated successfully!</div>";
            } 
            else 
            {
                $message = "<div style='background-color: #FF4980; color: white; border: 3px solid black; padding: 15px; font-weight: 900; margin-bottom: 20px;'><i class='fa-solid fa-triangle-exclamation' style='margin-right:8px;'></i> ERROR: Could not update password.</div>";
            }
        } 
        else 
        {
            $message = "<div style='background-color: #FF4980; color: white; border: 3px solid black; padding: 15px; font-weight: 900; margin-bottom: 20px;'><i class='fa-solid fa-triangle-exclamation' style='margin-right:8px;'></i> ERROR: Your new passwords do not match.</div>";
        }
    } 
    else 
    {
        $message = "<div style='background-color: #FF4980; color: white; border: 3px solid black; padding: 15px; font-weight: 900; margin-bottom: 20px;'><i class='fa-solid fa-triangle-exclamation' style='margin-right:8px;'></i> ERROR: Current password is incorrect.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security - PRINT.IT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=13">
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <div class="dashboard-wrapper">
        <aside class="dashboard-sidebar">
            <h2 class="sidebar-title">MAKER PANEL</h2>
            <a href="dashboard.php" class="sidebar-link"><i class="fa-solid fa-layer-group" style="margin-right: 10px;"></i> MY PRINTS</a>
            <a href="settings.php" class="sidebar-link active"><i class="fa-solid fa-gear" style="margin-right: 10px;"></i> SETTINGS</a>
        </aside>

        <main class="dashboard-main">
            <div class="page-title-row">
                <a href="settings.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i></a>
                <div>
                    <h1 style="font-weight: 900; font-size: 32px; text-transform: uppercase;">SECURITY</h1>
                    <p style="font-weight: 700; color: #4B5563;">Update your password to stay secure.</p>
                </div>
            </div>

            <div class="dashboard-form-box">
                
                <?php echo $message; ?>

                <form action="change-password.php" method="POST">
                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-lock" style="margin-right: 5px;"></i> CURRENT PASSWORD</label>
                        <input type="password" name="current_password" class="neo-input" placeholder="........" required>
                    </div>
                    
                    <hr class="divider" style="margin: 30px 0;">

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-key" style="margin-right: 5px;"></i> NEW PASSWORD</label>
                        <input type="password" name="new_password" class="neo-input" placeholder="........" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-check-double" style="margin-right: 5px;"></i> CONFIRM NEW PASSWORD</label>
                        <input type="password" name="confirm_password" class="neo-input" placeholder="........" required>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 30px;">
                        <a href="settings.php" style="color: #64748B; font-weight: 700; font-size: 14px; text-decoration: none;"><i class="fa-solid fa-xmark" style="margin-right: 5px;"></i> Cancel</a>
                        <button type="submit" class="neo-btn bg-yellow" style="padding: 15px 30px; font-size: 14px;">
                            <i class="fa-solid fa-shield-halved" style="margin-right: 8px;"></i> UPDATE PASSWORD
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <?php include 'includes/footer.php'; ?>

    <script src="js/main.js"></script>
</body>
</html>
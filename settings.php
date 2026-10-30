<?php
// Start session and connect to the database
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'includes/db.php';

// Check if user is actually logged in and if not then kick them to login page
if (!isset($_SESSION['user_id'])) 
{
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = "";

// this catches the form when you hit 'Save Changes'
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
    {
    // Grab the new data from the form, default to empty string if missing
    $new_username = isset($_POST['username']) ? $_POST['username'] : '';
    $new_email = isset($_POST['email']) ? $_POST['email'] : '';
    $new_bio = isset($_POST['bio']) ? $_POST['bio'] : '';

    // Update the database safely
    $update_stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, bio = ? WHERE user_id = ?");
    $update_stmt->bind_param("sssi", $new_username, $new_email, $new_bio, $user_id);
    
    if ($update_stmt->execute()) 
    {
        $success_message = "Settings successfully updated!";
    }
}

// This will fetch the current data every time the page loads
$fetch_stmt = $conn->prepare("SELECT username, email, bio FROM users WHERE user_id = ?");
$fetch_stmt->bind_param("i", $user_id);
$fetch_stmt->execute();
$result = $fetch_stmt->get_result();
$current_user = $result->fetch_assoc();

// Store the fetched data into variables so we can inject them into the HTML below
$db_username = isset($current_user['username']) ? $current_user['username'] : '';
$db_email = isset($current_user['email']) ? $current_user['email'] : '';
$db_bio = isset($current_user['bio']) ? $current_user['bio'] : '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - PRINT.IT</title>
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
            <div class="dashboard-header-flex">
                <div>
                    <h1 style="font-weight: 900; font-size: 40px; text-transform: uppercase;">SETTINGS</h1>
                    <p style="font-weight: 700; color: #4B5563;">Manage your maker profile and preferences.</p>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="profile-section">
                    <div class="profile-header-box">
                        <div class="profile-icon" style="display: flex; align-items: center; justify-content: center; font-size: 20px;"><i class="fa-solid fa-user"></i></div>
                        <div>
                            <h3 style="font-weight: 900; font-size: 20px;">PUBLIC PROFILE</h3>
                            <p style="font-size: 12px; font-weight: 700; color: #64748B;">How others see you on the marketplace.</p>
                        </div>
                    </div>

                    <?php if (!empty($success_message)): ?>
                        <div style="background-color: #B4FF36; border: 3px solid black; padding: 10px; font-weight: 900; margin-bottom: 20px; text-transform: uppercase;">
                            <i class="fa-solid fa-check" style="margin-right: 8px;"></i> <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>

                    <form action="settings.php" method="POST">
                        <div class="form-row-2">
                            <div class="form-group">
                                <label class="form-label"><i class="fa-solid fa-at" style="margin-right: 5px;"></i> USERNAME</label>
                                <input type="text" name="username" class="neo-input" value="<?php echo htmlspecialchars($db_username); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fa-solid fa-envelope" style="margin-right: 5px;"></i> EMAIL ADDRESS</label>
                                <input type="email" name="email" class="neo-input" value="<?php echo htmlspecialchars($db_email); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-pen" style="margin-right: 5px;"></i> BIO</label>
                            <textarea name="bio" class="neo-input" placeholder="Tell the world about your printing skills..."><?php echo htmlspecialchars($db_bio); ?></textarea>
                        </div>
                        <div style="text-align: right;">
                            <button type="submit" class="neo-btn bg-yellow" style="padding: 15px 30px; font-size: 14px;">
                                <i class="fa-solid fa-floppy-disk" style="margin-right: 8px;"></i> SAVE CHANGES
                            </button>
                        </div>
                    </form>
                </div>

                <div>
                    <div class="settings-widget bg-cyan">
                        <h3 style="font-weight: 900; font-size: 18px; margin-bottom: 20px;"><i class="fa-solid fa-bell" style="margin-right: 8px;"></i> NOTIFICATIONS</h3>
                        <div class="checkbox-group">
                            <input type="checkbox" checked> New Print Requests
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" checked> Order Updates
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox"> Marketing Emails
                        </div>
                    </div>

                    <div class="settings-widget bg-pink">
                        <h3 style="font-weight: 900; font-size: 18px; margin-bottom: 20px;"><i class="fa-solid fa-shield-halved" style="margin-right: 8px;"></i> SECURITY</h3>
                        <a href="change-password.php" class="neo-btn bg-white w-100" style="display:block; text-align:center; text-decoration:none; color:black; padding: 15px; font-size: 14px;">
                            <i class="fa-solid fa-key" style="margin-right: 8px;"></i> CHANGE PASSWORD
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <?php include 'includes/footer.php'; ?>

    <script src="js/main.js"></script>
</body>
</html>
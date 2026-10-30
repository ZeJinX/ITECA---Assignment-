<?php
session_start();
require 'includes/db.php';

// to check if they are NOT logged in, OR if their role is NOT 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') 
{
    //This will kick them immediately back to the login page
    header("Location: login.php");
    exit(); 
}

$message = "";

// Handle User Status Updates (Active / Suspended)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_user_status') 
    {
    $target_user_id = intval($_POST['target_user_id']);
    $new_status = $_POST['status']; 
    
    $update_stmt = $conn->prepare("UPDATE users SET status = ? WHERE user_id = ?");
    $update_stmt->bind_param("si", $new_status, $target_user_id);
    
    if ($update_stmt->execute()) {
        $message = "<div style='background-color: #B4FF36; border: 3px solid black; padding: 15px; font-weight: 900; margin-bottom: 20px; text-transform: uppercase;'>SUCCESS: User status updated!</div>";
    } 
    else 
    {
        $message = "<div style='background-color: #FF4980; color: white; border: 3px solid black; padding: 15px; font-weight: 900; margin-bottom: 20px;'>ERROR: Could not update user.</div>";
    }
}

// Fetch all users, and calculate their total prints
$sql = "SELECT 
            u.user_id, 
            u.full_name, 
            u.username, 
            u.status,
            (SELECT COUNT(*) FROM print_jobs pj WHERE pj.user_id = u.user_id OR pj.maker_id = u.user_id) as total_prints
        FROM users u
        WHERE u.role != 'admin'
        ORDER BY u.created_at DESC";
        
$users_result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Directory - PRINT.IT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=13">
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <div class="dashboard-wrapper">
        <aside class="dashboard-sidebar">
            <h2 class="sidebar-title">ADMIN ZONE</h2>
            <a href="admin-overview.php" class="sidebar-link"><i class="fa-solid fa-chart-line" style="margin-right: 10px;"></i> OVERVIEW</a>
            <a href="admin-users.php" class="sidebar-link active"><i class="fa-solid fa-users" style="margin-right: 10px;"></i> USERS</a>
            <a href="admin-transactions.php" class="sidebar-link"><i class="fa-solid fa-money-bill-transfer" style="margin-right: 10px;"></i> TRANSACTIONS</a>
        </aside>

        <main class="admin-main">
            <div class="dashboard-header-flex" style="align-items: center; margin-bottom: 40px;">
                <div>
                    <h1 style="font-weight: 900; font-size: 40px; text-transform: uppercase; margin-bottom: 5px;">USER DIRECTORY</h1>
                    <p style="font-weight: 700; font-size: 16px;">Manage makers and buyers on the platform.</p>
                </div>
                <div>
                    <input type="text" class="search-input" placeholder=" Search handles..." style="width: 300px; background: white;">
                </div>
            </div>

            <?php echo $message; ?>

            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr class="th-green">
                            <th>USER</th>
                            <th>STATUS</th>
                            <th>TOTAL PRINTS</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($users_result->num_rows > 0): ?>
                            <?php while($row = $users_result->fetch_assoc()): ?>
                                <?php $is_suspended = (strtolower($row['status']) == 'suspended'); ?>
                                
                                <tr style="<?php echo $is_suspended ? 'background-color: #ffe4e6;' : ''; ?>">
                                    <td>
                                        <div style="font-weight: 900; text-transform: uppercase;"><i class="fa-solid fa-user" style="margin-right: 8px;"></i><?php echo htmlspecialchars($row['full_name']); ?></div>
                                        <div style="color: #64748B; font-weight: 700; font-size: 12px;">@<?php echo htmlspecialchars($row['username']); ?></div>
                                    </td>
                                    
                                    <td style="font-weight: 900; text-transform: uppercase; color: <?php echo $is_suspended ? '#FF4980' : 'inherit'; ?>;">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </td>
                                    
                                    <td style="font-size: 20px; font-weight: 900;">
                                        <?php echo $row['total_prints']; ?>
                                    </td>
                                    
                                    <td>
                                        <form action="admin-users.php" method="POST" style="display: flex; gap: 5px; align-items: center; margin: 0;">
                                            <input type="hidden" name="action" value="update_user_status">
                                            <input type="hidden" name="target_user_id" value="<?php echo $row['user_id']; ?>">
                                            
                                            <select name="status" style="padding: 5px; font-weight: 900; border: 2px solid black; outline: none; font-size: 12px; background: white;">
                                                <option value="active" <?php if(!$is_suspended) echo 'selected'; ?>>ACTIVE</option>
                                                <option value="suspended" <?php if($is_suspended) echo 'selected'; ?>>SUSPENDED</option>
                                            </select>
                                            
                                            <button type="submit" class="neo-btn" style="background: black; color: white; padding: 5px 10px; font-size: 12px;">
                                                <i class="fa-solid fa-check" style="margin-right: 5px;"></i> SAVE
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center; font-weight: 900; padding: 30px;">No users found on the platform.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="js/main.js"></script>
</body>
</html>
<?php
//  Resume the user's session
session_start();

// Connect to the database
require 'includes/db.php';

// Admin check  if NOT logged in, OR role is NOT admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') 
{
    header("Location: login.php");
    exit(); 
}

$message = "";

// This will handle status updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_status') 
    {
    $job_id = $_POST['job_id'];
    $new_status = $_POST['status'];

    $update_stmt = $conn->prepare("UPDATE print_jobs SET status = ? WHERE job_id = ?");
    $update_stmt->bind_param("si", $new_status, $job_id);
    
    if ($update_stmt->execute()) 
    {
        $message = "<div style='background-color: #B4FF36; border: 3px solid black; padding: 15px; font-weight: 900; margin-bottom: 20px; text-transform: uppercase;'>SUCCESS: Job #$job_id updated to $new_status!</div>";
    } 
    else 
    {
        $message = "<div style='background-color: #FF4980; color: white; border: 3px solid black; padding: 15px; font-weight: 900; margin-bottom: 20px;'>ERROR: Could not update status.</div>";
    }
}

// Fetch all Print Jobs from the database
$sql = "SELECT print_jobs.*, users.username 
        FROM print_jobs 
        JOIN users ON print_jobs.user_id = users.user_id 
        ORDER BY print_jobs.created_at DESC";
$result = $conn->query($sql);

// Calculate Total Makers
$maker_count_result = $conn->query("SELECT COUNT(*) as total_makers FROM users WHERE role != 'admin'");
$total_makers = $maker_count_result->fetch_assoc()['total_makers'];

//Calculate Total Revenue (Completed Jobs * R25.00 Flat Fee)
$revenue_result = $conn->query("SELECT COUNT(*) as completed_count FROM print_jobs WHERE LOWER(status) = 'completed'");
$completed_count = $revenue_result->fetch_assoc()['completed_count'];
$total_revenue = $completed_count * 25.00;

//Check for Failed Transactions for System Alerts
$failed_result = $conn->query("SELECT COUNT(*) as failed_count FROM print_jobs WHERE LOWER(status) = 'failed'");
$failed_count = $failed_result->fetch_assoc()['failed_count'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Overview - PRINT.IT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=13">
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <div class="dashboard-wrapper">
        <aside class="dashboard-sidebar">
            <h2 class="sidebar-title">ADMIN ZONE</h2>
            <a href="admin-overview.php" class="sidebar-link active">
                <i class="fa-solid fa-chart-line" style="margin-right: 10px;"></i> OVERVIEW
            </a>
            <a href="admin-users.php" class="sidebar-link">
                <i class="fa-solid fa-users" style="margin-right: 10px;"></i> USERS
            </a>
            <a href="admin-transactions.php" class="sidebar-link">
                <i class="fa-solid fa-money-bill-transfer" style="margin-right: 10px;"></i> TRANSACTIONS
            </a>
        </aside>

        <main class="admin-main">
            <div style="margin-bottom: 40px;">
                <h1 style="font-weight: 900; font-size: 40px; text-transform: uppercase;">ADMIN COMMAND CENTER</h1>
                <p style="font-weight: 700; font-size: 16px;">God-mode activated.</p>
            </div>

            <?php echo $message; ?>

            <div class="admin-stats-grid">
                <div class="stat-card">
                    <div class="stat-icon bg-yellow" style="display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                    <div class="stat-title">TOTAL MAKERS</div>
                    <div class="stat-value"><?php echo number_format($total_makers); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #B4FF36; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-sack-dollar"></i>
                    </div>
                    <div class="stat-title">TOTAL REVENUE</div>
                    <div class="stat-value">R<?php echo number_format($total_revenue, 2); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-cyan" style="display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-print"></i>
                    </div>
                    <div class="stat-title">ACTIVE JOBS</div>
                    <div class="stat-value"><?php echo $result->num_rows; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-pink" style="display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-server"></i>
                    </div>
                    <div class="stat-title">SYSTEM UPTIME</div>
                    <div class="stat-value">99.9%</div>
                </div>
            </div>

            <div class="dashboard-grid">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 style="font-weight: 900; font-size: 24px;">ACTIVE PRINT JOBS</h3>
                        <button class="neo-btn bg-white" style="padding: 10px 20px; font-size: 12px;">
                            <i class="fa-solid fa-list" style="margin-right: 5px;"></i> VIEW ALL
                        </button>
                    </div>
                    
                    <div class="admin-table-wrapper">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>USER</th>
                                    <th>DETAILS</th>
                                    <th>STATUS</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) 
                                    {
                                    while($row = $result->fetch_assoc()) 
                                        {
                                        $status = strtolower($row['status']);
                                        ?>
                                        <tr>
                                            <td style="font-weight: 900;">JOB-0<?php echo $row['job_id']; ?></td>
                                            <td style="color: #FF4980; font-weight: 900;">@<?php echo htmlspecialchars($row['username']); ?></td>
                                            <td style="font-weight: 700; font-size: 14px;">
                                                <?php echo htmlspecialchars($row['title']); ?><br>
                                                <span style="color: #64748B; font-size: 12px;"><?php echo htmlspecialchars($row['material']); ?> | <?php echo htmlspecialchars($row['color_preference']); ?></span>
                                            </td>
                                            
                                            <td>
                                                <form action="admin-overview.php" method="POST" style="display: flex; gap: 5px; align-items: center; margin: 0;">
                                                    <input type="hidden" name="action" value="update_status">
                                                    <input type="hidden" name="job_id" value="<?php echo $row['job_id']; ?>">
                                                    
                                                    <select name="status" style="padding: 5px; font-weight: 900; border: 2px solid black; outline: none; font-size: 12px;">
                                                        <option value="queued" <?php if($status == 'queued') echo 'selected'; ?>>QUEUED</option>
                                                        <option value="printing" <?php if($status == 'printing') echo 'selected'; ?>>PRINTING</option>
                                                        <option value="completed" <?php if($status == 'completed') echo 'selected'; ?>>COMPLETED</option>
                                                    </select>
                                            </td>
                                            
                                            <td>
                                                    <button type="submit" class="neo-btn" style="background: black; color: white; padding: 5px 10px; font-size: 12px;">
                                                        <i class="fa-solid fa-check"></i> SAVE
                                                    </button>
                                                </form>
                                                <a href="<?php echo htmlspecialchars($row['file_path']); ?>" target="_blank" style="display: block; margin-top: 5px; font-size: 10px; font-weight: 900; text-decoration: underline; color: #00A8CC;">
                                                    <i class="fa-solid fa-download" style="margin-right: 3px;"></i> DL FILE
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } 
                                else 
                                {
                                    echo "<tr><td colspan='5' style='text-align: center; font-weight: 900; padding: 30px;'>No active jobs.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    <h3 style="font-weight: 900; font-size: 24px; margin-bottom: 20px;">SYSTEM ALERTS</h3>
                    
                    <?php if ($failed_count > 0): ?>
                        <div class="admin-alert">
                            <h4 style="font-weight: 900;"><i class="fa-solid fa-triangle-exclamation" style="margin-right: 5px;"></i> PAYMENT GATEWAY ERROR</h4>
                            <p style="font-weight: 700; color: #64748B; font-size: 14px;"><?php echo $failed_count; ?> transactions failed routing.</p>
                        </div>
                    <?php else: ?>
                        <div style="padding: 20px; border: 3px dashed var(--border-dark); text-align: center; font-weight: 900; background: white; margin-bottom: 20px;">
                            <i class="fa-solid fa-shield-check" style="margin-right: 5px;"></i> NO SYSTEM ALERTS
                        </div>
                    <?php endif; ?>

                    <button class="neo-btn bg-white w-100" style="padding: 15px; margin-top: 20px;">
                        <i class="fa-solid fa-wrench" style="margin-right: 5px;"></i> RUN DIAGNOSTICS
                    </button>
                </div>
            </div>
        </main>
    </div>
    <?php include 'includes/footer.php'; ?>

    <script src="js/main.js"></script>
</body>
</html>
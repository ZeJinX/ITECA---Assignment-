<?php
session_start();
require 'includes/db.php';

// check if the user must be logged in
if (!isset($_SESSION['user_id'])) 
{
    header("Location: login.php");
    exit();
}

//locks out admin
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: admin-zone.php");
    exit(); 
}


$user_id = $_SESSION['user_id'];

// Fetch print jobs that this user REQUESTED 
$stmt = $conn->prepare("SELECT * FROM print_jobs WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch incoming print jobs ASSIGNED to this user  that are NOT completed
$incoming_stmt = $conn->prepare("SELECT pj.*, u.username as customer_name FROM print_jobs pj JOIN users u ON pj.user_id = u.user_id WHERE pj.maker_id = ? AND LOWER(pj.status) != 'completed' ORDER BY pj.created_at DESC");
$incoming_stmt->bind_param("i", $user_id);
$incoming_stmt->execute();
$incoming_result = $incoming_stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PRINT.IT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=13">
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <div class="dashboard-wrapper">
        
        <aside class="dashboard-sidebar">
            <h2 class="sidebar-title">MAKER PANEL</h2>
            <a href="dashboard.php" class="sidebar-link active">
                <i class="fa-solid fa-layer-group" style="margin-right: 10px;"></i> MY PRINTS
            </a>
            <a href="settings.php" class="sidebar-link">
                <i class="fa-solid fa-gear" style="margin-right: 10px;"></i> SETTINGS
            </a>
        </aside>

        <main class="dashboard-main">
            
            <div class="dashboard-header-flex">
                <div>
                    <h1 style="font-weight: 900; font-size: 40px; text-transform: uppercase;">MY DASHBOARD</h1>
                    <p style="font-weight: 700; color: #4B5563;">Welcome back, Maker!</p>
                </div>
                <a href="new-request.php" class="neo-btn bg-yellow" style="text-decoration: none; display: inline-block; padding: 15px; font-size: 14px; color: black;">
                    <i class="fa-solid fa-plus" style="margin-right: 8px;"></i> NEW PRINT REQUEST
                </a>
            </div>

           
            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'status_updated'): ?>
                <div style='background-color: #B4FF36; border: 3px solid black; padding: 15px; font-weight: 900; margin-bottom: 20px; text-transform: uppercase;'>
                    SUCCESS: Order status updated!
                </div>
            <?php endif; ?>

            <div class="dashboard-grid">
                
                <div>
                    
                    <h3 class="section-title">INCOMING CUSTOM ORDERS</h3>
                    <hr class="divider" style="margin-top: 0; margin-bottom: 20px;">

                    <?php if ($incoming_result->num_rows > 0): ?>
                        <?php while($inc = $incoming_result->fetch_assoc()): ?>
                            
                            <div class="job-card" style="border-color: #00A8CC; background-color: #F0FBFF;">
                                <div class="job-icon-box" style="background-color: #00A8CC; color: white; display:flex; align-items:center; justify-content:center; font-weight:900;">IN</div>
                                <div class="job-details" style="width: 100%;">
                                    <div class="job-title-row" style="display: flex; justify-content: space-between; align-items: center;">
                                        <h4 style="font-weight: 900; font-size: 18px; text-transform: uppercase; margin:0;">
                                            <?php echo htmlspecialchars($inc['title']); ?>
                                        </h4>
                                        <span style="font-weight: 900; font-size: 18px; color: #10B981;">
                                            +R<?php echo number_format($inc['estimated_price'], 2); ?>
                                        </span>
                                    </div>
                                    <p style="color: #4B5563; font-size: 14px; font-weight: 700; margin: 5px 0;">
                                        From: @<?php echo htmlspecialchars($inc['customer_name']); ?> | Mat: <?php echo htmlspecialchars($inc['material']); ?>
                                    </p>
                                    
                                    <div style="background: white; border: 2px solid black; padding: 10px; font-size: 12px; margin-bottom: 10px; font-weight: 700;">
                                        Instructions: <?php echo htmlspecialchars($inc['instructions'] ? $inc['instructions'] : 'None provided.'); ?>
                                    </div>

                                    <div class="job-status-row" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                                        
                                        <form action="update-status.php" method="POST" style="display: flex; align-items: center; gap: 10px; margin: 0;">
                                            <input type="hidden" name="job_id" value="<?php echo $inc['job_id']; ?>">
                                            <label style="font-weight: 900; text-transform: uppercase; font-size: 12px;">STATUS:</label>
                                            <select name="status" class="neo-input" style="padding: 5px; min-height: 0; width: 150px; font-size: 12px; margin-bottom: 0;">
                                                <option value="Queued" <?php if(strtolower($inc['status']) == 'queued') echo 'selected'; ?>>Queued</option>
                                                <option value="Printing" <?php if(strtolower($inc['status']) == 'printing') echo 'selected'; ?>>Printing</option>
                                                <option value="Awaiting Payment" <?php if(strtolower($inc['status']) == 'awaiting payment') echo 'selected'; ?>>Awaiting Payment</option>
                                            </select>
                                            <button type="submit" class="neo-btn bg-cyan" style="padding: 5px 10px; font-size: 12px;">
                                                <i class="fa-solid fa-floppy-disk" style="margin-right: 5px;"></i> SAVE
                                            </button>
                                        </form>

                                        <a href="<?php echo htmlspecialchars($inc['file_path']); ?>" download class="neo-btn bg-yellow" style="padding: 5px 15px; font-size: 12px; text-decoration: none;">
                                            <i class="fa-solid fa-download" style="margin-right: 5px;"></i> DOWNLOAD STL
                                        </a>
                                    </div>
                                </div>
                            </div>

                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="padding: 30px; border: 3px dashed var(--border-dark); text-align: center; font-weight: 900; background: white; margin-bottom: 30px;">NO INCOMING ORDERS YET.</div>
                    <?php endif; ?>

                    <br>

                    <h3 class="section-title">MY REQUESTED PRINTS</h3>
                    <hr class="divider" style="margin-top: 0; margin-bottom: 20px;">

                    <?php
                    if ($result->num_rows > 0) 
                        {
                        while($row = $result->fetch_assoc()) 
                            {
                            
                            $status = strtolower($row['status']);
                            $box_color = "#E5E7EB"; 
                            
                            if ($status == 'printing') {
                                $box_color = "#FFD600"; 
                            } else if ($status == 'completed') {
                                $box_color = "#B4FF36";
                            } else if ($status == 'awaiting payment') {
                                $box_color = "#FF4980";
                            }

                            $date_formatted = date('M d, Y', strtotime($row['created_at']));
                            ?>
                            
                            <div class="job-card">
                                <div class="job-icon-box" style="background-color: <?php echo $box_color; ?>;"></div>
                                <div class="job-details" style="width: 100%;">
                                    <div class="job-title-row" style="display: flex; justify-content: space-between; align-items: center;">
                                        <h4 style="font-weight: 900; font-size: 18px; text-transform: uppercase; margin: 0;"><?php echo htmlspecialchars($row['title']); ?></h4>
                                        <span class="job-badge">JOB-0<?php echo $row['job_id']; ?></span>
                                    </div>
                                    <p style="color: #6B7280; font-size: 14px; font-weight: 700; margin: 5px 0;">Material: <?php echo htmlspecialchars($row['material']); ?> | Color: <?php echo htmlspecialchars($row['color_preference']); ?></p>
                                    
                                    <div class="job-status-row" style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                                        <div>
                                            <span style="text-transform: uppercase; font-weight: 900;"> STATUS: <?php echo $status; ?></span>
                                            <span style="margin-left: 10px; color: #64748B; font-weight: 700; font-size: 12px;"><?php echo $date_formatted; ?></span>
                                        </div>
                                        
                                        <?php if ($status == 'awaiting payment'): ?>
                                            <a href="job-checkout.php?job_id=<?php echo $row['job_id']; ?>" class="neo-btn bg-pink" style="padding: 8px 15px; font-size: 14px; font-weight: 900; color: white; text-decoration: none;">
                                                <i class="fa-solid fa-credit-card" style="margin-right: 5px;"></i> PAY R<?php echo number_format($row['estimated_price'], 2); ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <?php
                        }
                    } 
                    else 
                    {
                        echo "<div style='padding: 30px; border: 3px dashed var(--border-dark); text-align: center; font-weight: 900; background: white;'>NO PRINT JOBS IN QUEUE.</div>";
                    }
                    ?>

                </div>

                <div>
                    <h3 class="section-title">SELL & SERVICES</h3>
                    <hr class="divider" style="margin-top: 0; margin-bottom: 20px;">

                    <div class="service-card bg-cyan">
                        <h4 style="font-weight: 900; font-size: 20px; margin-bottom: 15px;">LIST A PRODUCT</h4>
                        <p style="font-weight: 700; margin-bottom: 20px; line-height: 1.5;">Got a sick design? Put it up on the marketplace and start earning.</p>
                        <a href="upload-product.php" class="neo-btn bg-white w-100" style="text-decoration: none; display: block; text-align: center; padding: 15px; font-size: 14px; color: black; box-sizing: border-box;">
                            <i class="fa-solid fa-upload" style="margin-right: 8px;"></i> UPLOAD STL
                        </a>
                    </div>

                    <div class="service-card bg-pink">
                        <h4 style="font-weight: 900; font-size: 20px; margin-bottom: 15px;">OFFER PRINTING SERVICES</h4>
                        <p style="font-weight: 700; margin-bottom: 20px; line-height: 1.5;">Your printer is sitting idle. Let others pay you to use it.</p>
                        <a href="set-rates.php" class="neo-btn bg-white w-100" style="text-decoration: none; display: block; text-align: center; padding: 15px; font-size: 14px; color: black; box-sizing: border-box;">
                            <i class="fa-solid fa-tags" style="margin-right: 8px;"></i> SET RATES
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
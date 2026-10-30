<?php
// Start session and connect to the database
if (session_status() === PHP_SESSION_NONE) 
{
    session_start();
}
require 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) 
{
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = "";

// This will capture and save the rates changes when the user hits save 
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    // Grab the new data for the users table
    $new_startup = isset($_POST['startup_fee']) ? $_POST['startup_fee'] : '0.00';
    $new_hourly = isset($_POST['hourly_rate']) ? $_POST['hourly_rate'] : '0.00';
    $new_accepting = isset($_POST['accepting_orders']) ? 1 : 0;

    // Clean up the money inputs 
    $clean_startup = str_replace(['R', ' ', ','], '', $new_startup);
    $clean_hourly = str_replace(['R', ' ', ','], '', $new_hourly);

    // Update the main users table 
    $update_stmt = $conn->prepare("UPDATE users SET startup_fee = ?, hourly_rate = ?, accepting_orders = ? WHERE user_id = ?");
    $update_stmt->bind_param("ssdi", $clean_startup, $clean_hourly, $new_accepting, $user_id);
    $update_stmt->execute();
 
    // Delete old materials for this user
    $del_stmt = $conn->prepare("DELETE FROM maker_materials WHERE user_id = ?");
    $del_stmt->bind_param("i", $user_id);
    $del_stmt->execute();

    //  Insert the new materials from the form
    if (isset($_POST['material_names']) && isset($_POST['material_costs'])) 
    {
        $mat_names = $_POST['material_names'];
        $mat_costs = $_POST['material_costs'];
        
        $ins_stmt = $conn->prepare("INSERT INTO maker_materials (user_id, material_name, cost_per_gram) VALUES (?, ?, ?)");
        
        for ($i = 0; $i < count($mat_names); $i++) 
        {
            $name = trim($mat_names[$i]);
            $cost = str_replace(['R', ' ', ','], '', $mat_costs[$i]);
            
            // Only save if the box isn't empty
            if (!empty($name) && $cost !== '') 
            {
                $ins_stmt->bind_param("isd", $user_id, $name, $cost);
                $ins_stmt->execute();
            }
        }
    }

    $success_message = "Rates and Materials successfully updated!";
}

// The reading logic of the page for base rates
$fetch_stmt = $conn->prepare("SELECT startup_fee, hourly_rate, accepting_orders FROM users WHERE user_id = ?");
$fetch_stmt->bind_param("i", $user_id);
$fetch_stmt->execute();
$result = $fetch_stmt->get_result();
$current_user = $result->fetch_assoc();

$db_startup = isset($current_user['startup_fee']) ? $current_user['startup_fee'] : '0.00';
$db_hourly = isset($current_user['hourly_rate']) ? $current_user['hourly_rate'] : '0.00';
$db_accepting = isset($current_user['accepting_orders']) ? $current_user['accepting_orders'] : 1;

// The reading logic for materials
$mat_stmt = $conn->prepare("SELECT material_name, cost_per_gram FROM maker_materials WHERE user_id = ?");
$mat_stmt->bind_param("i", $user_id);
$mat_stmt->execute();
$mat_result = $mat_stmt->get_result();

$saved_materials = [];
while ($row = $mat_result->fetch_assoc()) 
{
    $saved_materials[] = $row;
}

// If they have no materials saved yet, give them the 2 default rows to start with
if (empty($saved_materials)) 
{
    $saved_materials = [
        ['material_name' => 'PLA (Standard)', 'cost_per_gram' => '0.50'],
        ['material_name' => 'PETG (Strong)', 'cost_per_gram' => '0.75']
    ];
}
// But if they have less than 2 materials saved, add empty rows until they have 2 to match the form design
while (count($saved_materials) < 2) 
{
    $saved_materials[] = ['material_name' => '', 'cost_per_gram' => ''];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Rates - PRINT.IT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=13">
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <div class="dashboard-wrapper">
        <aside class="dashboard-sidebar">
            <h2 class="sidebar-title">MAKER PANEL</h2>
            <a href="dashboard.php" class="sidebar-link active"><i class="fa-solid fa-layer-group" style="margin-right: 10px;"></i> MY PRINTS</a>
            <a href="settings.php" class="sidebar-link"><i class="fa-solid fa-gear" style="margin-right: 10px;"></i> SETTINGS</a>
        </aside>

        <main class="dashboard-main">
            
            <div class="page-title-row">
                <a href="dashboard.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i></a>
                <div>
                    <h1 style="font-weight: 900; font-size: 32px; text-transform: uppercase;">BASE PRICING</h1>
                    <p style="font-weight: 700; color: #4B5563;">Manage your 3D printing service costs.</p>
                </div>
            </div>

            <div class="dashboard-form-box">
                
                <?php if (!empty($success_message)): ?>
                    <div style="background-color: #B4FF36; border: 3px solid black; padding: 10px; font-weight: 900; margin-bottom: 20px; text-transform: uppercase;">
                        <i class="fa-solid fa-check" style="margin-right: 8px;"></i> <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <form action="set-rates.php" method="POST">
                    
                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-play" style="margin-right: 5px;"></i> STARTUP FEE (ZAR)</label>
                            <input type="text" name="startup_fee" class="neo-input" value="<?php echo htmlspecialchars($db_startup); ?>">
                            <p style="font-size: 10px; font-weight: 700; color: #64748B; margin-top: 5px;">Base cost just to start the printer.</p>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-clock" style="margin-right: 5px;"></i> HOURLY RATE (ZAR)</label>
                            <input type="text" name="hourly_rate" class="neo-input" value="<?php echo htmlspecialchars($db_hourly); ?>">
                            <p style="font-size: 10px; font-weight: 700; color: #64748B; margin-top: 5px;">Cost per hour of print time.</p>
                        </div>
                    </div>

                    <hr class="divider" style="margin: 30px 0;">

                    <div style="margin-bottom: 20px;">
                        <h3 style="font-weight: 900; font-size: 20px; text-transform: uppercase;"><i class="fa-solid fa-cube" style="margin-right: 10px;"></i> MATERIAL RATES</h3>
                    </div>

                    <?php foreach ($saved_materials as $material): ?>
                        <div class="rates-row" style="grid-template-columns: 2fr 1fr;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" style="font-size: 12px;">MATERIAL</label>
                                <input type="text" name="material_names[]" class="neo-input" value="<?php echo htmlspecialchars($material['material_name']); ?>">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" style="font-size: 12px;">COST / GRAM (ZAR)</label>
                                <?php 
                                    $display_cost = $material['cost_per_gram'] !== '' ? 'R ' . number_format((float)$material['cost_per_gram'], 2) : '';
                                ?>
                                <input type="text" name="material_costs[]" class="neo-input" value="<?php echo $display_cost; ?>">
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <hr class="divider" style="margin: 30px 0;">

                    <h3 style="font-weight: 900; font-size: 20px; text-transform: uppercase; margin-bottom: 15px;"><i class="fa-solid fa-toggle-on" style="margin-right: 10px;"></i> STATUS</h3>
                    <div style="background-color: #ECFCCB; border: 3px solid black; padding: 15px; font-weight: 900; margin-bottom: 30px; display: flex; align-items: center;">
                        <input type="checkbox" name="accepting_orders" <?php echo ($db_accepting == 1) ? 'checked' : ''; ?> style="width: 20px; height: 20px; margin-right: 15px; border: 3px solid black;">
                        ACCEPTING NEW ORDERS
                    </div>

                    <button type="submit" class="neo-btn bg-pink w-100" style="padding: 15px; font-size: 16px;">
                        <i class="fa-solid fa-floppy-disk" style="margin-right: 8px;"></i> SAVE RATES
                    </button>
                </form>
            </div>
        </main>
    </div>
    <script src="js/main.js"></script>
</body>
</html>
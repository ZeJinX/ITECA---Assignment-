<?php
session_start();
require 'includes/db.php';

//check to ensure the user must be logged in!
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

//  Make sure we know WHICH maker is requesting a print from
if (!isset($_GET['maker_id']) && $_SERVER["REQUEST_METHOD"] != "POST") 
{
    // If they just clicked the sidebar link without picking a maker then it willsend them to the marketplace
    header("Location: marketplace.php#hire-a-maker");
    exit();
}

$maker_id = isset($_GET['maker_id']) ? intval($_GET['maker_id']) : (isset($_POST['maker_id']) ? intval($_POST['maker_id']) : 0);
$maker = null;
$materials = null;

// Fetch Maker's Base Rates and Materials for the form
if ($maker_id > 0) 
{
    $maker_stmt = $conn->prepare("SELECT username, startup_fee, hourly_rate FROM users WHERE user_id = ?");
    $maker_stmt->bind_param("i", $maker_id);
    $maker_stmt->execute();
    $maker_result = $maker_stmt->get_result();
    
    if ($maker_result->num_rows > 0) 
    {
        $maker = $maker_result->fetch_assoc();
    } 
    else 
    {
        die("Maker not found.");
    }

    $mat_stmt = $conn->prepare("SELECT id, material_name, cost_per_gram FROM maker_materials WHERE user_id = ?");
    $mat_stmt->bind_param("i", $maker_id);
    $mat_stmt->execute();
    $materials = $mat_stmt->get_result();
}

$message = "";

// Catch the form submission 
if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $title = $_POST['title'];
    $material = $_POST['material']; 
    $color = $_POST['color'];
    $infill = $_POST['infill'];
    $instructions = $_POST['instructions'];
    $user_id = $_SESSION['user_id']; 

    // This will capture the the math data
    $maker_id_post = isset($_POST['maker_id']) ? intval($_POST['maker_id']) : 0;
    $print_hours = isset($_POST['print_hours']) ? floatval($_POST['print_hours']) : 0;
    $print_weight = isset($_POST['print_weight']) ? floatval($_POST['print_weight']) : 0;

    // Recalculate the price on the server(this was absolutely horrid to understand and write)
    $final_price = 0;
    if ($maker_id_post > 0) 
    {
        $rate_stmt = $conn->prepare("SELECT startup_fee, hourly_rate FROM users WHERE user_id = ?");
        $rate_stmt->bind_param("i", $maker_id_post);
        $rate_stmt->execute();
        $rates = $rate_stmt->get_result()->fetch_assoc();

        $mat_stmt = $conn->prepare("SELECT cost_per_gram FROM maker_materials WHERE user_id = ? AND material_name = ?");
        $mat_stmt->bind_param("is", $maker_id_post, $material);
        $mat_stmt->execute();
        $mat_data = $mat_stmt->get_result()->fetch_assoc();
        $cost_per_gram = $mat_data ? $mat_data['cost_per_gram'] : 0;

        $time_cost = $print_hours * ($rates ? $rates['hourly_rate'] : 0);
        $material_cost = $print_weight * $cost_per_gram;
        $final_price = ($rates ? $rates['startup_fee'] : 0) + $time_cost + $material_cost;
    }

    // Handle the STL File Upload
    $target_dir = "uploads/stls/";

    // Quick check to make sure the folder exists, if not, create it!
    if (!file_exists($target_dir)) 
    {
        mkdir($target_dir, 0777, true);
    }

    $file_name = time() . "_" . basename($_FILES["stl_file"]["name"]);
    $target_file = $target_dir . $file_name;
    
    // Move the file and save to the database 
    if (move_uploaded_file($_FILES["stl_file"]["tmp_name"], $target_file)) 
    {
        $stmt = $conn->prepare("INSERT INTO print_jobs (user_id, maker_id, title, material, color_preference, infill_quality, instructions, file_path, estimated_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissssssd", $user_id, $maker_id_post, $title, $material, $color, $infill, $instructions, $target_file, $final_price);

        if ($stmt->execute()) 
        {
            $message = "<div style='background-color: #B4FF36; border: 3px solid black; padding: 15px; font-weight: 900; margin-bottom: 20px; text-transform: uppercase;'><i class='fa-solid fa-check' style='margin-right:8px;'></i> SUCCESS: Print job submitted directly to the Maker!</div>";
        } 
        else 
        {
            $message = "<div style='background-color: #FF4980; color: white; border: 3px solid black; padding: 15px; font-weight: 900; margin-bottom: 20px;'><i class='fa-solid fa-triangle-exclamation' style='margin-right:8px;'></i> ERROR: Database failed to save.</div>";
        }
    } 
    else
    {
        $message = "<div style='background-color: #FF4980; color: white; border: 3px solid black; padding: 15px; font-weight: 900; margin-bottom: 20px;'><i class='fa-solid fa-triangle-exclamation' style='margin-right:8px;'></i> ERROR: Failed to upload STL.</div>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Print Request - PRINT.IT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=10">
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
                <a href="marketplace.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i></a>
                <div>
                    <h1 style="font-weight: 900; font-size: 32px; text-transform: uppercase;">NEW PRINT REQUEST</h1>
                    <p style="font-weight: 700; color: #4B5563;">
                        <?php if ($maker): ?>
                            Requesting a custom job from <strong>@<?php echo htmlspecialchars($maker['username']); ?></strong>
                        <?php else: ?>
                            Submit your 3D model to get it printed.
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <div class="dashboard-form-box">

                <?php echo $message; ?>

                <form action="new-request.php" method="POST" enctype="multipart/form-data">
                    
                    <input type="hidden" name="maker_id" value="<?php echo $maker_id; ?>">

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-tag" style="margin-right:5px;"></i> PROJECT TITLE</label>
                        <input type="text" name="title" class="neo-input" placeholder="e.g. Master Sword Prop" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-file-arrow-up" style="margin-right:5px;"></i> UPLOAD FILE</label>
                        <div class="upload-area" style="position: relative; cursor: pointer;">
                            <input type="file" name="stl_file" accept=".stl,.obj,.png,.jpg,.jpeg" required style="width: 100%; padding: 20px; box-sizing: border-box; border: 2px dashed #00A8CC; background: #f8fafc; font-weight: 700;">
                            <p style="font-weight: 700; font-size: 12px; color: #64748B; margin-top: 10px;">Max file size: 50MB</p>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-cube" style="margin-right:5px;"></i> SELECT MATERIAL</label>
                            <select id="material_select" name="material" class="neo-input" required onchange="calculateTotal()">
                                <option value="" data-cost="0">-- Choose a Material --</option>
                                <?php if ($materials): ?>
                                    <?php while($mat = $materials->fetch_assoc()): ?>
                                        <option value="<?php echo htmlspecialchars($mat['material_name']); ?>" data-cost="<?php echo $mat['cost_per_gram']; ?>">
                                            <?php echo htmlspecialchars($mat['material_name']); ?> (R <?php echo $mat['cost_per_gram']; ?>/g)
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-palette" style="margin-right:5px;"></i> COLOR PREFERENCE</label>
                            <input type="text" name="color" class="neo-input" placeholder="e.g. Matte Black" required>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-clock" style="margin-right:5px;"></i> ESTIMATED PRINT TIME (HOURS)</label>
                            <input type="number" id="print_hours" name="print_hours" class="neo-input" min="0" step="0.5" required onkeyup="calculateTotal()" onchange="calculateTotal()">
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-weight-scale" style="margin-right:5px;"></i> ESTIMATED WEIGHT (GRAMS)</label>
                            <input type="number" id="print_weight" name="print_weight" class="neo-input" min="0" required onkeyup="calculateTotal()" onchange="calculateTotal()">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-sliders" style="margin-right:5px;"></i> INFILL & QUALITY</label>
                        <input type="text" name="infill" class="neo-input" placeholder="e.g. 20% Infill, 0.2mm layer height" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-pen" style="margin-right:5px;"></i> ADDITIONAL INSTRUCTIONS</label>
                        <textarea name="instructions" class="neo-input" placeholder="Any specific orientation requirements, supports needed, or other notes?"></textarea>
                    </div>

                    <hr class="divider" style="margin: 30px 0;">

                    <div style="background-color: #ECFCCB; border: 3px solid black; padding: 20px; margin-bottom: 30px;">
                        <h3 style="font-weight: 900; text-transform: uppercase; margin-bottom: 15px; font-size: 20px;"><i class="fa-solid fa-calculator" style="margin-right:10px;"></i> PRICE ESTIMATE</h3>
                        
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-weight: 700;">
                            <span>Startup Fee:</span>
                            <span>R <?php echo $maker ? number_format($maker['startup_fee'], 2) : '0.00'; ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-weight: 700;">
                            <span>Time Cost (R<?php echo $maker ? number_format($maker['hourly_rate'], 2) : '0.00'; ?>/hr):</span>
                            <span id="time_cost_display">R 0.00</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-weight: 700; border-bottom: 2px solid black; padding-bottom: 10px;">
                            <span>Material Cost:</span>
                            <span id="material_cost_display">R 0.00</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-weight: 900; font-size: 24px; margin-top: 10px;">
                            <span>TOTAL:</span>
                            <span id="total_price_display">R <?php echo $maker ? number_format($maker['startup_fee'], 2) : '0.00'; ?></span>
                        </div>
                    </div>

                    <button type="submit" class="neo-btn bg-yellow w-100" style="padding: 15px; font-size: 16px;">
                        <i class="fa-solid fa-paper-plane" style="margin-right:8px;"></i> SUBMIT REQUEST
                    </button>
                </form>
            </div>
        </main>
    </div>

    <script>
        const startupFee = <?php echo $maker ? $maker['startup_fee'] : 0; ?>;
        const hourlyRate = <?php echo $maker ? $maker['hourly_rate'] : 0; ?>;

        function calculateTotal() {
            const hours = parseFloat(document.getElementById('print_hours').value) || 0;
            const weight = parseFloat(document.getElementById('print_weight').value) || 0;
            
            const matSelect = document.getElementById('material_select');
            const costPerGram = parseFloat(matSelect.options[matSelect.selectedIndex].getAttribute('data-cost')) || 0;

            const timeCost = hours * hourlyRate;
            const materialCost = weight * costPerGram;
            const total = startupFee + timeCost + materialCost;

            document.getElementById('time_cost_display').innerText = 'R ' + timeCost.toFixed(2);
            document.getElementById('material_cost_display').innerText = 'R ' + materialCost.toFixed(2);
            document.getElementById('total_price_display').innerText = 'R ' + total.toFixed(2);
        }
    </script>
    <script src="js/main.js"></script>
</body>
</html>
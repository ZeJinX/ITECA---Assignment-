<?php
//the start of the session 
if (session_status() === PHP_SESSION_NONE)
    {
        session_start();
    }
    require 'includes/db.php';

    //Get the user ID from the URL and also a function to check if there is no user ID
    if(!isset($_GET['maker_id']))
        {
            header("Location: marketplace.php");
            exit();
        }

$maker_id = intval($_GET['maker_id']);

// Fetch the users profile data and base rates
$profile_stmt = $conn->prepare("SELECT username, bio, startup_fee, hourly_rate, accepting_orders FROM users WHERE user_id = ?");
$profile_stmt->bind_param("i", $maker_id);
$profile_stmt->execute();
$profile_result = $profile_stmt->get_result();

// check if the user doesn't exist, show an error
if ($profile_result->num_rows === 0) 
{
    die("<h1 style='padding: 50px; font-family: sans-serif; text-transform: uppercase;'>Maker not found in the database.</h1>");
}

$maker = $profile_result->fetch_assoc();

// Fetch the users custom materials
$mat_stmt = $conn->prepare("SELECT material_name, cost_per_gram FROM maker_materials WHERE user_id = ?");
$mat_stmt->bind_param("i", $maker_id);
$mat_stmt->execute();
$mat_result = $mat_stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@<?php echo htmlspecialchars($maker['username']); ?>'s Profile - PRINT.IT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=13">
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <div style="max-width: 900px; margin: 60px auto; padding: 0 5%;">
        
        <div style="margin-bottom: 30px;">
            <a href="marketplace.php" class="back-btn" style="text-decoration: none;"><i class="fa-solid fa-arrow-left"></i></a>
            <span style="font-weight: 900; text-transform: uppercase; margin-left: 15px;">Back to Marketplace</span>
        </div>

        <div class="neo-box" style="padding: 40px; margin-bottom: 40px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px;">
                <div>
                    <h1 style="font-size: 48px; font-weight: 900; text-transform: uppercase; margin-bottom: 10px;">
                        @<?php echo htmlspecialchars($maker['username']); ?>
                    </h1>
                    
                    <?php if ($maker['accepting_orders'] == 1): ?>
                        <span class="neo-badge bg-green"><i class="fa-solid fa-circle-check" style="margin-right: 5px;"></i> Accepting Orders</span>
                    <?php else: ?>
                        <span class="neo-badge bg-pink"><i class="fa-solid fa-circle-xmark" style="margin-right: 5px;"></i> Currently Booked</span>
                    <?php endif; ?>
                </div>
                
                <!-- Added icon to profile box -->
                <div class="profile-icon" style="width: 100px; height: 100px; font-size: 40px; display: flex; align-items: center; justify-content: center; border: 2px solid black;">
                    <i class="fa-solid fa-user-gear"></i>
                </div>
            </div>

            <h3 style="font-weight: 900; font-size: 18px; margin-bottom: 10px;"><i class="fa-solid fa-address-card" style="margin-right: 8px;"></i> ABOUT THE MAKER</h3>
            <p style="font-weight: 700; color: #4B5563; line-height: 1.6; margin-bottom: 40px;">
                <?php echo !empty($maker['bio']) ? htmlspecialchars($maker['bio']) : "This maker hasn't written a bio yet, but their prints speak for themselves."; ?>
            </p>

            <hr class="divider">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 40px;">
                
                <div>
                    <h3 style="font-weight: 900; font-size: 20px; text-transform: uppercase; margin-bottom: 20px;"><i class="fa-solid fa-calculator" style="margin-right: 8px;"></i> BASE PRICING</h3>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px; border-bottom: 3px solid black; padding-bottom: 10px;">
                        <span style="font-weight: 900;"><i class="fa-solid fa-play" style="margin-right: 8px;"></i> Startup Fee</span>
                        <span style="font-weight: 900; color: #00A8CC;">R <?php echo number_format($maker['startup_fee'], 2); ?></span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; border-bottom: 3px solid black; padding-bottom: 10px;">
                        <span style="font-weight: 900;"><i class="fa-solid fa-clock" style="margin-right: 8px;"></i> Hourly Rate</span>
                        <span style="font-weight: 900; color: #00A8CC;">R <?php echo number_format($maker['hourly_rate'], 2); ?> / hr</span>
                    </div>
                </div>

                <div>
                    <h3 style="font-weight: 900; font-size: 20px; text-transform: uppercase; margin-bottom: 20px;"><i class="fa-solid fa-cube" style="margin-right: 8px;"></i> AVAILABLE MATERIALS</h3>
                    
                    <?php if ($mat_result->num_rows > 0): ?>
                        <?php while ($material = $mat_result->fetch_assoc()): ?>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 15px; border-bottom: 3px solid black; padding-bottom: 10px;">
                                <span style="font-weight: 900;"><?php echo htmlspecialchars($material['material_name']); ?></span>
                                <span style="font-weight: 900; color: #FF69B4;">R <?php echo number_format($material['cost_per_gram'], 2); ?> / g</span>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="font-weight: 700; font-style: italic;">No specific materials listed.</p>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <div style="text-align: center; margin-bottom: 60px;">
            <?php if ($maker['accepting_orders'] == 1): ?>
                <a href="new-request.php?maker_id=<?php echo $maker_id; ?>" class="neo-btn bg-yellow" style="font-size: 24px; padding: 20px 40px; width: 100%; max-width: 600px;">
                    <i class="fa-solid fa-paper-plane" style="margin-right: 10px;"></i> REQUEST A CUSTOM PRINT
                </a>
            <?php else: ?>
                <button class="neo-btn" disabled style="background-color: #E2E8F0; color: #94A3B8; cursor: not-allowed; font-size: 24px; padding: 20px 40px; width: 100%; max-width: 600px;">
                    <i class="fa-solid fa-lock" style="margin-right: 10px;"></i> NOT ACCEPTING ORDERS
                </button>
            <?php endif; ?>
        </div>

    </div>

    <script src="js/main.js"></script>
</body>
</html>
<?php
session_start();
require 'includes/db.php';

// Ensure they are logged in and actually passed a job ID
if (!isset($_SESSION['user_id']) || !isset($_GET['job_id'])) 
{
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$job_id = $_GET['job_id'];

// Fetch the exact custom job to get the price
$stmt = $conn->prepare("SELECT * FROM print_jobs WHERE job_id = ? AND user_id = ? AND LOWER(status) = 'awaiting payment'");
$stmt->bind_param("ii", $job_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Security check to see if the job doesn't exist or isn't awaiting payment or kick them out
if ($result->num_rows === 0)
 {
    die("Order not found or not ready for payment.");
}

$job = $result->fetch_assoc();
$total = number_format($job['estimated_price'], 2, '.', '');
$item_name = "Custom Print: " . htmlspecialchars($job['title']);

// PayFast Sandbox Credentials 
$merchant_id = '10048014';
$merchant_key = 'kt89v0pyv4ory';
$return_url = 'https://printitzone.com/job-success.php?job_id=' . $job_id;
$cancel_url = 'https://printitzone.com/job-cancelled.php?job_id=' . $job_id;
 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Invoice - PRINT.IT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=15">
</head>
<body style="background-color: #f8fafc;">

    <?php include 'includes/navbar.php'; ?>

    <div style="max-width: 600px; margin: 80px auto; padding: 40px; background: white; border: 4px solid black; box-shadow: 8px 8px 0px black;">
        
        <h1 style="font-weight: 900; font-size: 32px; text-transform: uppercase; border-bottom: 4px solid black; padding-bottom: 20px; margin-bottom: 30px;">
            <i class="fa-solid fa-file-invoice-dollar" style="margin-right: 10px;"></i> CUSTOM ORDER INVOICE
        </h1>

        <div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-weight: 700; font-size: 18px;">
            <span><i class="fa-solid fa-cube" style="margin-right: 8px;"></i> <?php echo $item_name; ?></span>
            <span>R<?php echo $total; ?></span>
        </div>

        <div style="display: flex; justify-content: space-between; font-weight: 900; font-size: 24px; border-top: 4px solid black; padding-top: 20px; margin-bottom: 40px;">
            <span>TOTAL TO PAY:</span>
            <span style="color: #FF4980;">R<?php echo $total; ?></span>
        </div>

        <form action="https://sandbox.payfast.co.za/eng/process" method="POST">
            <input type="hidden" name="merchant_id" value="<?php echo $merchant_id; ?>">
            <input type="hidden" name="merchant_key" value="<?php echo $merchant_key; ?>">
            <input type="hidden" name="return_url" value="<?php echo $return_url; ?>">
            <input type="hidden" name="cancel_url" value="<?php echo $cancel_url; ?>">
            <input type="hidden" name="amount" value="<?php echo $total; ?>">
            <input type="hidden" name="item_name" value="<?php echo $item_name; ?>">
            
            <button type="submit" class="neo-btn bg-pink w-100" style="padding: 20px; font-size: 20px; text-transform: uppercase; cursor: pointer;">
                <i class="fa-solid fa-lock" style="margin-right: 10px;"></i> PROCEED TO SECURE PAYMENT
            </button>
        </form>
        
        <a href="dashboard.php" style="display: block; text-align: center; margin-top: 20px; color: #64748B; font-weight: 700; text-decoration: none;">
            <i class="fa-solid fa-arrow-left" style="margin-right: 5px;"></i> Cancel and return to dashboard
        </a>

    </div>

    <script src="js/main.js"></script>
</body>
</html>
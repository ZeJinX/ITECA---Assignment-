<?php
session_start();
require 'includes/db.php';

// Check if they arrived properly with a transaction ID
if (!isset($_SESSION['user_id']) || !isset($_GET['trans_id'])) 
{
    header("Location: cart.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$transaction_id = $_GET['trans_id'];

// Update the database to show this specific transaction failed or was cancelled
$update_stmt = $conn->prepare("UPDATE transactions SET status = 'failed' WHERE transaction_id = ? AND buyer_id = ?");
$update_stmt->bind_param("si", $transaction_id, $user_id);
$update_stmt->execute();

// This will put the inventory back on the digital shelves since the payment failed!
$restore_stmt = $conn->prepare("
    UPDATE products 
    JOIN cart ON products.product_id = cart.product_id 
    SET products.quantity = products.quantity + cart.quantity 
    WHERE cart.user_id = ?
");
$restore_stmt->bind_param("i", $user_id);
$restore_stmt->execute();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - PRINT.IT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=12">
</head>
<body style="background-color: #f8fafc;">

    <?php include 'includes/navbar.php'; ?>

    <div style="max-width: 600px; margin: 80px auto; padding: 40px; background: white; border: 4px solid black; box-shadow: 8px 8px 0px black; text-align: center;">
        
        <div style="width: 80px; height: 80px; background: #FF4980; border: 4px solid black; margin: 0 auto 20px auto; display: flex; justify-content: center; align-items: center; font-weight: 900; font-size: 40px; color: white;">
            <i class="fa-solid fa-circle-xmark"></i>
        </div>
        
        <h1 style="font-weight: 900; font-size: 36px; text-transform: uppercase; margin-bottom: 10px;">PAYMENT CANCELLED</h1>
        
        <p style="font-weight: 700; color: #64748B; margin-bottom: 30px; font-size: 16px;">
            Your transaction was cancelled or declined. Don't worry, no funds were deducted from your account.
        </p>
        
        <div style="background: #f1f5f9; border: 2px solid black; padding: 15px; margin-bottom: 30px; display: inline-block;">
            <p style="font-weight: 900; font-size: 14px; margin: 0; text-transform: uppercase;">
                <i class="fa-solid fa-hashtag" style="margin-right: 5px;"></i> Reference: <?php echo htmlspecialchars($transaction_id); ?>
            </p>
        </div>

        <a href="cart.php" style="display: block; background-color: #FF4980; color: black; border: 3px solid black; padding: 20px; font-size: 20px; font-weight: 900; text-decoration: none; text-transform: uppercase; box-shadow: 4px 4px 0px black;">
            <i class="fa-solid fa-bag-shopping" style="margin-right: 10px;"></i> RETURN TO CART
        </a>

    </div>

    <script src="js/main.js"></script>
</body>
</html>
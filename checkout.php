<?php
session_start();
require 'includes/db.php';

//  Check to ensure they be logged in
if (!isset($_SESSION['user_id']))
{
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Calculate the exact total from the cart
$calc_stmt = $conn->prepare("
    SELECT SUM(products.price * cart.quantity) as subtotal 
    FROM cart 
    JOIN products ON cart.product_id = products.product_id 
    WHERE cart.user_id = ?
");

$calc_stmt->bind_param("i", $user_id);
$calc_stmt->execute();
$result = $calc_stmt->get_result();
$row = $result->fetch_assoc();

$subtotal = isset($row['subtotal']) ? $row['subtotal'] : 0;

// Prevent checkout if cart is empty
if ($subtotal <= 0) 
{
    header("Location: cart.php");
    exit();
}

$platform_fee = 25.00;
$final_amount = $subtotal + $platform_fee; // Subtotal + R25 Platform Fee

$maker_stmt = $conn->prepare("
    SELECT products.maker_id 
    FROM cart 
    JOIN products ON cart.product_id = products.product_id 
    WHERE cart.user_id = ? 
    LIMIT 1
");
$maker_stmt->bind_param("i", $user_id);
$maker_stmt->execute();
$maker_result = $maker_stmt->get_result();

// Default to 1 (Marketplace) just in case, but overwrite it with the actual maker
$maker_id = 1; 
if ($maker_row = $maker_result->fetch_assoc()) {
    $maker_id = $maker_row['maker_id'];
}
$maker_stmt->close();


//this will make a unique string ID and insert using the exact table columns
$transaction_id = uniqid('TRX-'); 


$trans_stmt = $conn->prepare("INSERT INTO transactions (transaction_id, maker_id, buyer_id, amount, platform_fee, status) VALUES (?, ?, ?, ?, ?, 'pending')");
$trans_stmt->bind_param("siidd", $transaction_id, $maker_id, $user_id, $final_amount, $platform_fee);
$trans_stmt->execute();


// This subtracts the items in the cart from the main marketplace inventory
$stock_stmt = $conn->prepare("
    UPDATE products 
    JOIN cart ON products.product_id = cart.product_id 
    SET products.quantity = products.quantity - cart.quantity 
    WHERE cart.user_id = ?
");
$stock_stmt->bind_param("i", $user_id);
$stock_stmt->execute();


// PayFast Sandbox Configuration
$merchant_id = '10048014'; //  My Sandbox ID
$merchant_key = 'kt89v0pyv4ory'; //  My Sandbox Key


// Your LIVE Hostinger Domain
$live_domain = 'https://printitzone.com'; 

// Where PayFast should send the user after paying
$return_url = $live_domain . '/payment-success.php?trans_id=' . $transaction_id;
$cancel_url = $live_domain . '/payment-cancelled.php?trans_id=' . $transaction_id;

// The ITN Webhook so PayFast can securely update your database in the background
$notify_url = $live_domain . '/payfast-webhook.php'; 
// ==========================================

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - PRINT.IT</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css?v=12">
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <div class="checkout-container">
        
        <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 20px;">
        
            <div class="icon-box-large bg-pink" style="margin-bottom: 15px; display: flex; align-items: center; justify-content: center; font-size: 32px;">
                <i class="fa-solid fa-credit-card"></i>
            </div>
            <h1 style="font-weight: 900; font-size: 40px; text-transform: uppercase; margin: 0;">CHECKOUT</h1>
            <p style="font-weight: 700; color: #64748B; margin-top: 10px;">Complete your payment securely with PayFast</p>
        </div>

        <div class="checkout-box">
            
            <div style="text-align: center; margin-bottom: 30px;">
                <h3 style="font-weight: 900; font-size: 18px; color: #64748B; margin-bottom: 10px;">TOTAL AMOUNT</h3>
                <div style="font-weight: 900; font-size: 50px; color: #00A8CC;">R <?php echo number_format($final_amount, 2); ?></div>
            </div>

            <hr class="divider">

            <div class="payfast-box">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div style="font-weight: 900; font-size: 18px; display: flex; align-items: center; gap: 10px;">
                        <span style="color: #22C55E;"><i class="fa-solid fa-shield-halved"></i></span> PAYFAST
                    </div>
                    <div style="display: flex; gap: 5px;">
                        <div style="width: 30px; height: 20px; background: #2563EB; border: 2px solid black;"></div>
                        <div style="width: 30px; height: 20px; background: #DC2626; border: 2px solid black; border-radius: 10px;"></div>
                        <div style="width: 30px; height: 20px; background: #FACC15; border: 2px solid black;"></div>
                    </div>
                </div>
                
                <hr class="divider">
                
                <div style="text-align: center; font-weight: 900; font-size: 12px; margin-top: 15px;">
                     <i class="fa-solid fa-lock" style="margin-right: 5px;"></i> 100% Secure Encrypted Payment
                </div>
            </div>

            <form action="https://sandbox.payfast.co.za/eng/process" method="POST" style="margin: 0;">
                <input type="hidden" name="merchant_id" value="<?php echo $merchant_id; ?>">
                <input type="hidden" name="merchant_key" value="<?php echo $merchant_key; ?>">
                <input type="hidden" name="amount" value="<?php echo number_format($final_amount, 2, '.', ''); ?>">
                <input type="hidden" name="item_name" value="PRINT.IT Order #<?php echo $transaction_id; ?>">
                
                <input type="hidden" name="return_url" value="<?php echo $return_url; ?>">
                <input type="hidden" name="cancel_url" value="<?php echo $cancel_url; ?>">
                <input type="hidden" name="notify_url" value="<?php echo $notify_url; ?>">
                
                <button type="submit" class="neo-btn w-100" style="background-color: #B4FF36; padding: 20px; font-size: 20px; display: flex; justify-content: center; align-items: center; gap: 10px; cursor: pointer;">
                    PAY R <?php echo number_format($final_amount, 2); ?> NOW <span><i class="fa-solid fa-arrow-right"></i></span>
                </button>
            </form>

            <p style="text-align: center; font-weight: 900; font-size: 10px; color: #94A3B8; margin-top: 20px; text-transform: uppercase; letter-spacing: 1px;">
                You will be redirected to your dashboard upon completion.
            </p>

        </div>
    </div>
    <?php include 'includes/footer.php'; ?>

    <script src="js/main.js"></script>
</body>
</html>
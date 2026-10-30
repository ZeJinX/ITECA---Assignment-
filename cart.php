<?php
session_start();
require 'includes/db.php';

// check to see if the user must be logged in to view a cart
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// This will grab the cart items AND the matching product details
$sql = "SELECT cart.cart_id, cart.quantity, products.product_id, products.product_name, products.price, products.image_file_path, products.category 
        FROM cart 
        JOIN products ON cart.product_id = products.product_id 
        WHERE cart.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();


$subtotal = 0;
$platform_fee = 25; // my fee
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cart - PRINT.IT</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="css/style.css?v=12">
    </head>
    <body>
        <?php include 'includes/navbar.php'; ?>

    <div class="cart-container">
        
        <div class="cart-header">
            <div class="icon-box-large bg-cyan" style="display: flex; align-items: center; justify-content: center; font-size: 32px;">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <h1 style="font-weight: 900; font-size: 40px; text-transform: uppercase; margin: 0;">YOUR CART</h1>
        </div>

        <div class="cart-grid">
            
            <div>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        
                        // Calculate the price for this specific item row (Price x Quantity)
                        $item_total = $row['price'] * $row['quantity'];
                        
                        // Add it to the running subtotal
                        $subtotal += $item_total;
                        ?>
                        
                        <div class="cart-item">
                            <img src="<?php echo htmlspecialchars($row['image_file_path']); ?>" alt="Product Image" class="cart-item-img" style="object-fit: cover;">
                            
                            <div class="cart-item-details">
                                <h3 style="font-weight: 900; font-size: 18px; text-transform: uppercase;"><?php echo htmlspecialchars($row['product_name']); ?></h3>
                                <p style="font-size: 14px; font-weight: 700; color: #64748B; margin-top: 5px;"><?php echo htmlspecialchars($row['category']); ?></p>
                                <div class="cart-price">R <?php echo number_format($row['price'], 2); ?></div>
                            </div>
                            
                            <div class="cart-actions">
                                <div class="qty-controls">
                                    <button class="qty-btn"><i class="fa-solid fa-minus"></i></button>
                                    <input type="text" class="qty-input" value="<?php echo $row['quantity']; ?>" readonly>
                                    <button class="qty-btn"><i class="fa-solid fa-plus"></i></button>
                                </div>
                                
                                <form action="remove-from-cart.php" method="POST" style="margin:0;">
                                    <input type="hidden" name="cart_id" value="<?php echo $row['cart_id']; ?>">
                                    <button type="submit" class="delete-btn" style="cursor: pointer;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <?php
                    }
                } 
                else 
                {
                    // This will show if the cart is completely empty
                    echo "<div style='padding: 40px; border: 4px dashed var(--border-dark); text-align: center; font-weight: 900; background: white;'>
                            <i class='fa-solid fa-box-open' style='font-size: 32px; color: var(--border-dark); margin-bottom: 15px; display: block;'></i>
                            YOUR CART IS EMPTY.
                          </div>";
                }
                ?>
            </div>

            <div>
                <div class="order-summary">
                    <h2 style="font-weight: 900; font-size: 24px; margin-bottom: 20px;">ORDER SUMMARY</h2>
                    <hr class="divider" style="margin-bottom: 20px;">
                    
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>R <?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Platform Fee</span>
                        <span>R <?php echo $subtotal > 0 ? number_format($platform_fee, 2) : "0.00"; ?></span>
                    </div>

                    <hr class="divider" style="margin-top: 20px; margin-bottom: 20px;">

                    <div class="summary-row summary-total">
                        <span>Total</span>
                        <?php $final_total = $subtotal > 0 ? ($subtotal + $platform_fee) : 0; ?>
                        <span>R <?php echo number_format($final_total, 2); ?></span>
                    </div>

                    <?php if ($subtotal > 0): ?>
                        <a href="checkout.php" class="neo-btn bg-yellow w-100" style="display: block; text-align: center; text-decoration: none; color: black; padding: 15px; font-size: 16px; margin-bottom: 15px;">
                            <i class="fa-solid fa-credit-card" style="margin-right: 8px;"></i> CHECKOUT
                        </a>
                    <?php else: ?>
                        <div class="neo-btn w-100" style="background: #E5E7EB; color: #9CA3AF; text-align: center; padding: 15px; font-size: 16px; margin-bottom: 15px; cursor: not-allowed;">
                            <i class="fa-solid fa-lock" style="margin-right: 8px;"></i> CHECKOUT
                        </div>
                    <?php endif; ?>
                    
                    <div style="text-align: center;">
                        <a href="marketplace.php" style="color: black; font-weight: 900; text-decoration: none; font-size: 14px;">
                            <i class="fa-solid fa-arrow-left" style="margin-right: 5px;"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <?php include 'includes/footer.php'; ?>

    <script src="js/main.js"></script>
        
    </body>
</html>

<?php
session_start();
require 'includes/db.php';

// Check to see if user is logged in 
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check to see if the form is sent to the cart ID
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cart_id']))
 {
    $user_id = $_SESSION['user_id'];
    $cart_id = $_POST['cart_id'];

    // Delete the item
    $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    
    if ($stmt->execute()) 
    {
        // Bounce them right back to the cart page
        header("Location: cart.php?removed=success");
        exit();
    } 
    else 
    {
        // If the database query failed
        header("Location: cart.php?error=db_fail");
        exit();
    }
} 
else 
{
    // If they arrived by accident, send them back to the cart
    header("Location: cart.php");
    exit();
}
?>





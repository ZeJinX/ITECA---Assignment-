<?php
session_start();
require 'includes/db.php';

// Kick them out to login if they try to add something without an account
if (!isset($_SESSION['user_id']))
{
    header("Location: login.php");
    exit();
}

// Check if a product ID was actually sent via the button
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) 
{
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];

    //  Insert the item into the new cart table
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
    $stmt->bind_param("ii", $user_id, $product_id);
    
    if ($stmt->execute()) 
    {
        // Send them back to the marketplace with a success message in the URL
        header("Location: marketplace.php?added=success");
        exit();
    }
    else 
    {
        echo "Error adding to cart.";
    }
} 
else
{
    // If they find themselves onto this page by accident, send them back
    header("Location: marketplace.php");
    exit();
}
?>